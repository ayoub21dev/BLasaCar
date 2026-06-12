<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountProfileRequest extends FormRequest
{
    /**
     * Trim account profile fields before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'email' => trim((string) $this->input('email', '')),
            'phone' => trim((string) $this->input('phone', '')),
        ]);
    }

    /**
     * Allow signed-in users to update their account profile.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Validate account profile fields and keep email/phone unique.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()?->id)],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($this->user()?->id)],
        ];
    }
}
