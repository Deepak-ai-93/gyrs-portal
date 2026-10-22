<?php

namespace App\Services\Ai;

use RuntimeException;

class LlmResponseException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $inputTokens = 0,
        public readonly int $outputTokens = 0,
    ) {
        parent::__construct($message);
    }

    public static function unavailable(string $reason, int $inputTokens = 0, int $outputTokens = 0): self
    {
        return new self('The AI service is temporarily unavailable: '.$reason, $inputTokens, $outputTokens);
    }

    public static function invalidJson(int $inputTokens = 0, int $outputTokens = 0): self
    {
        return new self('The AI service returned an invalid response.', $inputTokens, $outputTokens);
    }
}
