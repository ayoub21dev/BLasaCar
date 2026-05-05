<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('public');

        $traveler = User::factory()->traveler()->create();

        $response = $this->actingAs($traveler)->post(route('drivers.onboarding.store'), [
            'cin_number' => 'BK987654',
            'cin_photo' => UploadedFile::fake()->image('cin-front.jpg'),
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
        $this->assertNotNull($driverProfile->cin_photo);
        Storage::disk('public')->assertExists($driverProfile->cin_photo);

        $this->assertDatabaseHas('vehicles', [
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
        ]);
    }

    public function test_cin_photo_is_required_to_become_a_driver(): void
    {
        $traveler = User::factory()->traveler()->create();

        $this->actingAs($traveler)->post(route('drivers.onboarding.store'), [
            'cin_number' => 'BK987655',
            'vehicle_brand' => 'Dacia',
            'vehicle_model' => 'Logan',
        ])->assertSessionHasErrors('cin_photo');

        $this->assertDatabaseMissing('driver_profiles', [
            'user_id' => $traveler->id,
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
