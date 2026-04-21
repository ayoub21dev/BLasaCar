<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('06########'),
            'password_hash' => 'password',
            'profile_photo' => null,
            'email_verified' => true,
            'phone_verified' => false,
            'account_status' => 'active',
            'role' => User::ROLE_TRAVELER,
            'suspended_at' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified' => false,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function driver(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_DRIVER,
        ]);
    }

    public function traveler(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_TRAVELER,
        ]);
    }
}
