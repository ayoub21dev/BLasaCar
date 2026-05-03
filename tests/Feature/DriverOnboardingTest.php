<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_traveler_can_open_driver_onboarding_page(): void
    {
        $this->withoutVite();

        $traveler = User::factory()->traveler()->create();

        $this->actingAs($traveler)
            ->get(route('drivers.onboarding.create'))
            ->assertOk()
            ->assertSee('Become a', false)
            ->assertSee('driver', false);
    }

    public function test_traveler_can_become_a_driver_with_first_vehicle(): void
    {
        $traveler = User::factory()->traveler()->create();

        $response = $this->actingAs($traveler)->post(route('drivers.onboarding.store'), [
            'cin_number' => 'BK987654',
            'vehicle_brand' => 'Dacia',
            'vehicle_model' => 'Logan',
        ]);

        $response->assertRedirect(route('dashboards.driver'));

        $this->assertDatabaseHas('users', [
            'id' => $traveler->id,
            'role' => User::ROLE_DRIVER,
        ]);

        $driverProfile = DriverProfile::query()
            ->where('user_id', $traveler->id)
            ->firstOrFail();

        $this->assertFalse($driverProfile->cin_verified);

        $this->assertDatabaseHas('vehicles', [
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
        ]);
    }

    public function test_driver_is_redirected_away_from_onboarding(): void
    {
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'DR123456',
            'cin_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Renault',
            'model' => 'Clio',
            'photo' => null,
        ]);

        $this->actingAs($driver)
            ->get(route('drivers.onboarding.create'))
            ->assertRedirect(route('dashboards.driver'));
    }
}
