<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class BookRideRequest extends FormRequest
{
    /**
     * Allow only traveler accounts to request ride seats.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_TRAVELER;
    }

    /**
     * Validate the number of seats a traveler can request at once.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'seats' => ['required', 'integer', 'min:1', 'max:4'],
        ];
    }
}
