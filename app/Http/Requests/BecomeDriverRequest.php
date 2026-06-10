<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class BecomeDriverRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cin_number' => strtoupper(trim((string) $this->input('cin_number', ''))),
            'vehicle_brand' => trim((string) $this->input('vehicle_brand', '')),
            'vehicle_model' => trim((string) $this->input('vehicle_model', '')),
        ]);
    }

    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->role === User::ROLE_TRAVELER
            && $user->account_status === 'active'
            && $user->driverProfile === null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'cin_number' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{1,3}[0-9]{4,10}$/', 'unique:driver_profiles,cin_number'],
            'cin_front_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cin_back_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'vehicle_brand' => ['required', 'string', 'max:80'],
            'vehicle_model' => ['required', 'string', 'max:80'],
        ];
    }
}
