<?php

namespace App\Http\Requests\Admin;

use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        $permissions = array_values(array_intersect(
            (array) $this->input('permissions', []),
            AdminPermissions::all(),
        ));

        $this->merge(['permissions' => $permissions]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $administratorId = $this->route('administrator')?->getKey();
        $creating = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($administratorId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(AdminPermissions::all())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
