<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\DriverIdentityPhotos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_verify_a_pending_driver_profile(): void
    {
        Storage::fake(DriverIdentityPhotos::DISK);
        Storage::disk(DriverIdentityPhotos::DISK)->put('cin/front/aa987654.jpg', 'id-photo-front');
        Storage::disk(DriverIdentityPhotos::DISK)->put('cin/back/aa987654.jpg', 'id-photo-back');

        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987654',
            'cin_photo' => 'cin/front/aa987654.jpg',
            'cin_front_photo' => 'cin/front/aa987654.jpg',
            'cin_back_photo' => 'cin/back/aa987654.jpg',
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

    public function test_admin_cannot_verify_a_driver_profile_without_both_cin_photos(): void
    {
        Storage::fake(DriverIdentityPhotos::DISK);
        Storage::disk(DriverIdentityPhotos::DISK)->put('cin/front/aa987655.jpg', 'id-photo-front');

        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987655',
            'cin_photo' => 'cin/front/aa987655.jpg',
            'cin_front_photo' => 'cin/front/aa987655.jpg',
            'cin_back_photo' => null,
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

    public function test_admin_can_open_protected_cin_photo(): void
    {
        Storage::fake(DriverIdentityPhotos::DISK);
        Storage::disk(DriverIdentityPhotos::DISK)->put('cin/front/aa987656.jpg', 'id-photo-front');

        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987656',
            'cin_photo' => 'cin/front/aa987656.jpg',
            'cin_front_photo' => 'cin/front/aa987656.jpg',
            'cin_back_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.driver-profiles.cin', [$driverProfile, DriverIdentityPhotos::FRONT]))
            ->assertOk();
    }

    public function test_non_admin_cannot_open_protected_cin_photo(): void
    {
        Storage::fake(DriverIdentityPhotos::DISK);
        Storage::disk(DriverIdentityPhotos::DISK)->put('cin/front/aa987657.jpg', 'id-photo-front');

        $traveler = User::factory()->traveler()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA987657',
            'cin_photo' => 'cin/front/aa987657.jpg',
            'cin_front_photo' => 'cin/front/aa987657.jpg',
            'cin_back_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($traveler)
            ->get(route('admin.driver-profiles.cin', [$driverProfile, DriverIdentityPhotos::FRONT]))
            ->assertRedirect(route('dashboards.traveler'));
    }

    public function test_non_admin_cannot_verify_a_driver_profile(): void
    {
        $traveler = User::factory()->traveler()->create();
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'BB987654',
            'cin_photo' => null,
            'cin_front_photo' => null,
            'cin_back_photo' => null,
            'cin_verified' => false,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $this->actingAs($traveler)
            ->patch(route('admin.driver-profiles.verify', $driverProfile))
            ->assertRedirect(route('dashboards.traveler'));

        $this->assertFalse($driverProfile->fresh()->cin_verified);
    }

    public function test_admin_can_suspend_and_activate_a_user_from_workflow_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $traveler = User::factory()->traveler()->create();

        $this->actingAs($admin)
            ->from(route('dashboards.admin.users'))
            ->patch(route('admin.users.suspend', $traveler))
            ->assertRedirect(route('dashboards.admin.users'))
            ->assertSessionHas('status', 'User suspended.');

        $this->assertSame('suspended', $traveler->fresh()->account_status);
        $this->assertNotNull($traveler->fresh()->suspended_at);

        $this->actingAs($admin)
            ->from(route('dashboards.admin.users'))
            ->patch(route('admin.users.activate', $traveler))
            ->assertRedirect(route('dashboards.admin.users'))
            ->assertSessionHas('status', 'User activated.');

        $this->assertSame('active', $traveler->fresh()->account_status);
        $this->assertNull($traveler->fresh()->suspended_at);
    }

    public function test_admin_can_add_a_note_to_a_ride_without_changing_status(): void
    {
        $admin = User::factory()->admin()->create();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'confirmed',
            'booked_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(route('dashboards.admin.rides'))
            ->patch(route('admin.rides.note', $ride), [
                'admin_note' => 'Reported by users for review.',
            ])
            ->assertRedirect(route('dashboards.admin.rides'))
            ->assertSessionHas('status', 'Ride note saved.');

        $this->assertSame('scheduled', $ride->fresh()->status);
        $this->assertSame(3, $ride->fresh()->available_seats);
        $this->assertSame('Reported by users for review.', $ride->fresh()->admin_note);
        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_admin_ride_note_route_ignores_status_changes(): void
    {
        $admin = User::factory()->admin()->create();
        $ride = $this->createScheduledRide();
        $ride->update([
            'status' => 'cancelled',
            'available_seats' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('dashboards.admin.rides'))
            ->patch(route('admin.rides.note', $ride), [
                'status' => 'scheduled',
                'admin_note' => 'Restore this ride.',
            ])
            ->assertRedirect(route('dashboards.admin.rides'))
            ->assertSessionHas('status', 'Ride note saved.');

        $this->assertSame('cancelled', $ride->fresh()->status);
        $this->assertSame(0, $ride->fresh()->available_seats);
        $this->assertSame('Restore this ride.', $ride->fresh()->admin_note);
    }

    private function createScheduledRide(): Ride
    {
        $driver = User::factory()->driver()->create();
        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'CC987654',
            'cin_photo' => null,
            'cin_front_photo' => null,
            'cin_back_photo' => null,
            'cin_verified' => true,
            'avg_rating' => 0,
            'total_trips' => 0,
        ]);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
            'photo' => null,
        ]);

        $casablanca = City::query()->create(['name' => 'Casablanca']);
        $rabat = City::query()->create(['name' => 'Rabat']);

        return Ride::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 3,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ]);
    }
}
