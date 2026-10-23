<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class OtpRequestRequest extends FormRequest
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => is_string($this->phone) ? preg_replace('/\D/', '', $this->phone) : $this->phone,
        ]);
    }
}
