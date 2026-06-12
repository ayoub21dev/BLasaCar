<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Allow only traveler accounts to submit driver reviews.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_TRAVELER;
    }

    /**
     * Validate the rating and optional review text.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
