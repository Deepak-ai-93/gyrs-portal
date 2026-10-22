<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class LlmClient
{
    public function isConfigured(): bool
    {
        return (bool) config('ai.enabled')
            && filled(config('ai.api_key'))
            && filled(config('ai.provider'))
            && filled(config('ai.model'));
    }

    /**
     * Ask the configured provider for a JSON object.
     *
     * @return array{data: array<string, mixed>, input_tokens: int, output_tokens: int, model: string}
     */
    public function generateJson(string $systemPrompt, string $userPrompt, int $maxTokens = 3200): array
    {
        if (! $this->isConfigured()) {
            throw LlmResponseException::unavailable('not configured');
        }

        $provider = mb_strtolower((string) config('ai.provider'));
        $response = $this->send($provider, $systemPrompt, $userPrompt, $maxTokens);

        if ($response->failed() && in_array($response->status(), [429, 500, 502, 503, 504], true)) {
            usleep(700_000);
            $response = $this->send($provider, $systemPrompt, $userPrompt, $maxTokens);
        }

        if ($response->failed()) {
            throw LlmResponseException::unavailable('HTTP '.$response->status());
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw LlmResponseException::invalidJson();
        }

        $text = $this->responseText($provider, $payload);
        $inputTokens = $this->inputTokens($provider, $payload);
        $outputTokens = $this->outputTokens($provider, $payload);

        if ($text === null) {
            throw LlmResponseException::invalidJson($inputTokens, $outputTokens);
        }

        try {
            $data = $this->decodeJson($text);
        } catch (LlmResponseException) {
            $repairPrompt = "Repair the response below into one valid JSON object matching the required shape. Preserve its content, do not invent facts, and return JSON only.\n\n<response>\n"
                .mb_substr($text, 0, (int) config('ai.max_input_chars', 16000))
                ."\n</response>";
            $repair = $this->send($provider, $systemPrompt, $repairPrompt, $maxTokens);

            if ($repair->failed()) {
                throw LlmResponseException::unavailable('HTTP '.$repair->status(), $inputTokens, $outputTokens);
            }

            $repairPayload = $repair->json();
            $repairText = is_array($repairPayload) ? $this->responseText($provider, $repairPayload) : null;

            if ($repairText === null) {
                throw LlmResponseException::invalidJson(
                    $inputTokens + (is_array($repairPayload) ? $this->inputTokens($provider, $repairPayload) : 0),
                    $outputTokens + (is_array($repairPayload) ? $this->outputTokens($provider, $repairPayload) : 0),
                );
            }

            $repairInputTokens = $this->inputTokens($provider, $repairPayload);
            $repairOutputTokens = $this->outputTokens($provider, $repairPayload);

            try {
                $data = $this->decodeJson($repairText);
            } catch (LlmResponseException) {
                throw LlmResponseException::invalidJson(
                    $inputTokens + $repairInputTokens,
                    $outputTokens + $repairOutputTokens,
                );
            }

            $inputTokens += $repairInputTokens;
            $outputTokens += $repairOutputTokens;
        }

        return [
            'data' => $data,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'model' => (string) config('ai.model'),
        ];
    }

    /** @param array<string, mixed> $payload */
    private function responseText(string $provider, array $payload): ?string
    {
        $text = $provider === 'gemini'
            ? data_get($payload, 'candidates.0.content.parts.0.text')
            : data_get($payload, 'choices.0.message.content');

        return is_string($text) && trim($text) !== '' ? $text : null;
    }

    /** @param array<string, mixed> $payload */
    private function inputTokens(string $provider, array $payload): int
    {
        return (int) ($provider === 'gemini'
            ? data_get($payload, 'usageMetadata.promptTokenCount', 0)
            : data_get($payload, 'usage.prompt_tokens', 0));
    }

    /** @param array<string, mixed> $payload */
    private function outputTokens(string $provider, array $payload): int
    {
        return (int) ($provider === 'gemini'
            ? data_get($payload, 'usageMetadata.candidatesTokenCount', 0)
            : data_get($payload, 'usage.completion_tokens', 0));
    }

    private function send(string $provider, string $systemPrompt, string $userPrompt, int $maxTokens): Response
    {
        $timeout = max(5, (int) config('ai.timeout', 30));

        if ($provider === 'gemini') {
            $base = (string) (config('ai.base_url') ?: 'https://generativelanguage.googleapis.com/v1beta');
            $endpoint = rtrim($base, '/').'/models/'.rawurlencode((string) config('ai.model')).':generateContent';

            return Http::timeout($timeout)
                ->acceptJson()
                ->withQueryParameters(['key' => (string) config('ai.api_key')])
                ->post($endpoint, [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $userPrompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => $maxTokens,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
        }

        $defaultBaseUrl = $provider === 'openrouter'
            ? 'https://openrouter.ai/api/v1'
            : 'https://api.openai.com/v1';
        $base = (string) (config('ai.base_url') ?: $defaultBaseUrl);
        $endpoint = str_ends_with($base, '/chat/completions')
            ? $base
            : rtrim($base, '/').'/chat/completions';

        $request = Http::timeout($timeout)
            ->acceptJson()
            ->withToken((string) config('ai.api_key'));

        if ($provider === 'openrouter') {
            $request = $request->withHeaders([
                'HTTP-Referer' => (string) config('app.url'),
                'X-Title' => (string) config('app.name'),
            ]);
        }

        return $request->post($endpoint, [
            'model' => (string) config('ai.model'),
            'temperature' => 0.2,
            'max_tokens' => $maxTokens,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/iu', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/u', '', $text) ?? $text;

        try {
            $decoded = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');

            if ($start === false || $end === false || $end <= $start) {
                throw LlmResponseException::invalidJson();
            }

            try {
                $decoded = json_decode(substr($text, $start, $end - $start + 1), true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                throw LlmResponseException::invalidJson();
            }
        }

        if (! is_array($decoded)) {
            throw LlmResponseException::invalidJson();
        }

        return $decoded;
    }
}
