<?php

namespace App\Http\Requests\Api\Mobile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class BookRideRequest extends FormRequest
{
    /**
     * Allow only mobile traveler accounts to request seats.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_TRAVELER;
    }

    /**
     * Validate the mobile seat request count.
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
