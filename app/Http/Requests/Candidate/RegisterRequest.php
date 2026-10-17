<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'digits:10', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'district_id' => ['required', 'exists:districts,id'],
            'qualification' => ['required', 'string', 'max:255'],
            'cgpa' => ['nullable', 'numeric', 'between:0,10'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => is_string($this->phone) ? preg_replace('/\D/', '', $this->phone) : $this->phone,
        ]);
    }
}
