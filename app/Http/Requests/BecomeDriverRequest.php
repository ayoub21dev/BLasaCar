<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class BecomeDriverRequest extends FormRequest
{
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
            'cin_number' => ['required', 'string', 'max:50', 'unique:driver_profiles,cin_number'],
            'vehicle_brand' => ['required', 'string', 'max:80'],
            'vehicle_model' => ['required', 'string', 'max:80'],
        ];
    }
}
