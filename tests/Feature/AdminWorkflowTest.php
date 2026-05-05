<?php

namespace Tests\Feature;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_verify_a_pending_driver_profile(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('cin/aa987654.jpg', 'id-photo');

        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987654',
            'cin_photo' => 'cin/aa987654.jpg',
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('dashboards.admin'))
            ->patch(route('admin.driver-profiles.verify', $driverProfile))
            ->assertRedirect(route('dashboards.admin'));

        $this->assertTrue($driverProfile->fresh()->cin_verified);
    }

    public function test_admin_cannot_verify_a_driver_profile_without_cin_photo(): void
    {
        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987655',
            'cin_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('dashboards.admin'))
            ->patch(route('admin.driver-profiles.verify', $driverProfile))
            ->assertRedirect(route('dashboards.admin'))
            ->assertSessionHasErrors('driver_profile');

        $this->assertFalse($driverProfile->fresh()->cin_verified);
    }

    public function test_non_admin_cannot_verify_a_driver_profile(): void
    {
        $traveler = User::factory()->traveler()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'BB987654',
            'cin_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($traveler)
            ->patch(route('admin.driver-profiles.verify', $driverProfile))
            ->assertRedirect(route('dashboards.traveler'));

        $this->assertFalse($driverProfile->fresh()->cin_verified);
    }
}
