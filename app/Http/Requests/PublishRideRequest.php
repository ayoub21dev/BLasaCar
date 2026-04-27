<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PublishRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_DRIVER;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $driverProfileId = $this->user()?->driverProfile?->id;

        return [
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')->where('driver_profile_id', $driverProfileId),
            ],
            'departure_city_id' => ['required', 'integer', 'exists:cities,id'],
            'arrival_city_id' => ['required', 'integer', 'different:departure_city_id', 'exists:cities,id'],
            'departure_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'seats_offered' => ['required', 'integer', 'min:1', 'max:4'],
            'price_per_seat' => ['required', 'numeric', 'min:1', 'max:10000'],
            'meeting_point' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $date = $this->string('departure_date')->toString();
                $time = $this->string('departure_time')->toString();

                if ($date === '' || $time === '' || $validator->errors()->isNotEmpty()) {
                    return;
                }

                $departureAt = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}");

                if ($departureAt->lessThanOrEqualTo(now())) {
                    $validator->errors()->add('departure_time', 'The departure date and time must be in the future.');
                }
            },
        ];
    }
}
