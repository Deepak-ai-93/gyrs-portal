<?php

namespace App\Services\Ai;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Smalot\PdfParser\Parser;
use Throwable;
use ZipArchive;

class ResumeTextExtractor
{
    /**
     * Extract text from an uploaded résumé.
     *
     * @return array{text: string, source_type: string, original_filename: string, file_size: int, content_hash: string}
     */
    public function fromUpload(UploadedFile $file, ?string $candidateName = null): array
    {
        $maxBytes = (int) config('ai.max_file_kb', 3072) * 1024;
        $size = (int) $file->getSize();

        if ($size > $maxBytes) {
            throw ValidationException::withMessages([
                'resume' => 'The résumé must be smaller than '.round($maxBytes / 1024).' KB.',
            ]);
        }

        $path = $file->getRealPath();
        $contents = $path ? file_get_contents($path) : false;

        if ($contents === false || $contents === '') {
            throw ValidationException::withMessages([
                'resume' => 'We could not read that file. Please try again.',
            ]);
        }

        $extension = mb_strtolower($file->getClientOriginalExtension());

        $text = match ($extension) {
            'pdf' => $this->extractPdf($contents),
            'docx' => $this->extractDocx($path),
            'txt' => $this->normalise($contents),
            default => throw ValidationException::withMessages([
                'resume' => 'Upload a PDF, DOCX or TXT résumé.',
            ]),
        };

        return [
            'text' => $this->prepareForAnalysis($text, $candidateName),
            'source_type' => $extension,
            'original_filename' => 'resume.'.$extension,
            'file_size' => $size,
            'content_hash' => hash('sha256', $contents),
        ];
    }

    /**
     * @return array{text: string, source_type: string, original_filename: string, file_size: int, content_hash: string}
     */
    public function fromText(string $text, ?string $candidateName = null): array
    {
        $text = $this->normalise($text);

        if (mb_strlen($text) < 40) {
            throw ValidationException::withMessages([
                'resume_text' => 'Paste a little more résumé text so we can analyse it properly.',
            ]);
        }

        return [
            'text' => $this->prepareForAnalysis($text, $candidateName),
            'source_type' => 'text',
            'original_filename' => 'pasted-resume.txt',
            'file_size' => strlen($text),
            'content_hash' => hash('sha256', $text),
        ];
    }

    private function extractPdf(string $contents): string
    {
        if (! str_starts_with($contents, '%PDF-')) {
            throw ValidationException::withMessages([
                'resume' => 'That file does not look like a valid PDF.',
            ]);
        }

        try {
            $pdf = (new Parser)->parseContent($contents);
            $text = $pdf->getText();
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'resume' => 'We could not open this PDF. If it is password-protected or scanned, paste the text instead.',
            ]);
        }

        if (trim($text) === '') {
            throw ValidationException::withMessages([
                'resume' => 'This PDF appears to be scanned and has no readable text. Paste the résumé text instead.',
            ]);
        }

        return $text;
    }

    private function extractDocx(?string $path): string
    {
        if ($path === null) {
            throw ValidationException::withMessages([
                'resume' => 'We could not read that DOCX file.',
            ]);
        }

        $zip = new ZipArchive;

        $contents = file_get_contents($path);

        if ($contents === false || ! str_starts_with($contents, "PK\x03\x04")) {
            throw ValidationException::withMessages([
                'resume' => 'That file does not look like a valid DOCX document.',
            ]);
        }

        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
                'resume' => 'That file does not look like a valid DOCX document.',
            ]);
        }

        try {
            if ($zip->numFiles > 500) {
                throw ValidationException::withMessages([
                    'resume' => 'That DOCX file is too complex to process.',
                ]);
            }

            $xml = $zip->getFromName('word/document.xml');

            if ($xml === false || $xml === '') {
                throw ValidationException::withMessages([
                    'resume' => 'That DOCX file does not contain readable document text.',
                ]);
            }

            if (strlen($xml) > 8 * 1024 * 1024) {
                throw ValidationException::withMessages([
                    'resume' => 'That DOCX file is too large to process.',
                ]);
            }
        } finally {
            $zip->close();
        }

        $document = new DOMDocument;

        if (! $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
            throw ValidationException::withMessages([
                'resume' => 'We could not read the text inside that DOCX file.',
            ]);
        }

        $xpath = new DOMXPath($document);
        $paragraphs = $xpath->query('//*[local-name()="p"]');
        $lines = [];

        if ($paragraphs !== false) {
            foreach ($paragraphs as $paragraph) {
                $parts = [];

                foreach ($xpath->query('.//*[local-name()="t"]', $paragraph) ?: [] as $node) {
                    $parts[] = $node->textContent;
                }

                $line = trim(implode('', $parts));

                if ($line !== '') {
                    $lines[] = $line;
                }
            }
        }

        return implode("\n", $lines);
    }

    private function normalise(string $text): string
    {
        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $text) ?? $text;
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function prepareForAnalysis(string $text, ?string $candidateName): string
    {
        $text = $this->redactPii($text, $candidateName);
        $limit = (int) config('ai.max_input_chars', 16000);

        if (mb_strlen($text) > $limit) {
            $text = mb_substr($text, 0, $limit);
        }

        if (mb_strlen($text) < 40) {
            throw ValidationException::withMessages([
                'resume' => 'We could not read enough text from that file. Paste the résumé text instead.',
            ]);
        }

        return $text;
    }

    private function redactPii(string $text, ?string $candidateName): string
    {
        if (filled($candidateName)) {
            $namePattern = '/(?<![\p{L}\p{N}])'.preg_quote(trim($candidateName), '/').'(?![\p{L}\p{N}])/iu';
            $text = preg_replace($namePattern, '[name removed]', $text) ?? $text;
        }

        $text = preg_replace('/^\s*(?:(?:permanent|current|residential|present)\s+)?address\s*[:\-].*$/imu', '[address removed]', $text) ?? $text;
        $text = preg_replace('/\b(?:dob|date of birth)\s*[:\-]?\s*\d{1,2}[.\/-]\d{1,2}[.\/-]\d{2,4}\b/iu', '[date removed]', $text) ?? $text;
        $text = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/iu', '[email removed]', $text) ?? $text;
        $text = preg_replace('/(?<!\d)(?:\+?91[\s\-]?)?[6-9]\d{9}(?!\d)/u', '[phone removed]', $text) ?? $text;
        $text = preg_replace('/(?<!\d)(?:\d{4}[\s\-]?){2}\d{4}(?!\d)/u', '[id removed]', $text) ?? $text;
        $text = preg_replace('/\b[A-Z]{5}\d{4}[A-Z]\b/iu', '[id removed]', $text) ?? $text;
        $text = preg_replace('/\b[A-Z]\d{7}\b/iu', '[id removed]', $text) ?? $text;

        return $text;
    }
}
