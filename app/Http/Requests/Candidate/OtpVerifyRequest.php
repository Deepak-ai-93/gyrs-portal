<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'digits:10'],
            'code' => ['required', 'string', 'digits:'.$this->codeLength()],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => is_string($this->phone) ? preg_replace('/\D/', '', $this->phone) : $this->phone,
            'code' => is_string($this->code) ? preg_replace('/\D/', '', $this->code) : $this->code,
        ]);
    }

    private function codeLength(): int
    {
        return min(8, max(4, (int) config('otp.code_length', 6)));
    }
}
