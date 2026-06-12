<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountPasswordRequest extends FormRequest
{
    /**
     * Allow signed-in users to change their password.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Validate the current password plus the new confirmed password.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
