<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $identifier = $this->input('identifier')
            ?? $this->input('email')
            ?? $this->input('phone');

        $this->merge([
            'identifier' => is_string($identifier) ? trim($identifier) : $identifier,
        ]);
    }
}
