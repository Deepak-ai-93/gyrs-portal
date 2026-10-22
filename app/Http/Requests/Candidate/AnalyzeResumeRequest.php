<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isCandidate();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxKb = (int) config('ai.max_file_kb', 3072);

        return [
            'resume' => ['nullable', 'file', 'mimes:pdf,docx,txt', 'max:'.$maxKb, 'required_without:resume_text'],
            'resume_text' => ['nullable', 'string', 'min:40', 'max:20000', 'required_without:resume'],
            'target_role' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxKb = (int) config('ai.max_file_kb', 3072);

        return [
            'resume.required_without' => 'Upload a résumé or paste the résumé text.',
            'resume.mimes' => 'Upload a PDF, DOCX or TXT résumé.',
            'resume.max' => 'The résumé must be smaller than '.$maxKb.' KB.',
            'resume_text.required_without' => 'Paste the résumé text or upload a file.',
            'consent.accepted' => 'Please confirm that you understand how your résumé will be used.',
        ];
    }
}
