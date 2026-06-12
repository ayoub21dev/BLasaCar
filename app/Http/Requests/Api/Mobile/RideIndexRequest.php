<?php

namespace App\Http\Requests\Api\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class RideIndexRequest extends FormRequest
{
    /**
     * Let guests and signed-in users search mobile rides.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate optional mobile ride search filters.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'departure_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'arrival_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'departure_date' => ['nullable', 'date'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:4'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
