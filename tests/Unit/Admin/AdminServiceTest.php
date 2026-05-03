<?php

namespace Tests\Unit\Admin;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\Admin\AdminService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AdminServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_dashboard_metrics(): void
    {
        $service = new AdminService;

        $driver = User::factory()->create();
        DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'AA123456',
            'cin_photo' => 'cin/driver.jpg',
            'cin_verified' => true,
            'avg_rating' => 4.8,
            'total_trips' => 10,
        ]);

        $suspendedUser = User::factory()->create([
            'account_status' => 'suspended',
            'suspended_at' => now(),
        ]);

        $traveler = User::factory()->create();

        $departureCity = City::query()->create(['name' => 'Casablanca']);
        $arrivalCity = City::query()->create(['name' => 'Rabat']);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driver->driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
            'photo' => null,
        ]);

        $scheduledRide = Ride::query()->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 3,
            'meeting_point' => 'Gare Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ]);

        Ride::query()->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $arrivalCity->id,
            'arrival_city_id' => $departureCity->id,
            'departure_time' => now()->subDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 0,
            'meeting_point' => 'Agdal',
            'notes' => null,
            'admin_note' => null,
            'status' => 'completed',
        ]);

        Booking::query()->create([
            'ride_id' => $scheduledRide->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'confirmed',
            'booked_at' => now(),
        ]);

        $metrics = $service->dashboardMetrics();

        $this->assertSame(3, $metrics['total_users']);
        $this->assertSame(2, $metrics['active_users']);
        $this->assertSame(1, $metrics['suspended_users']);
        $this->assertSame(1, $metrics['verified_drivers']);
        $this->assertSame(1, $metrics['scheduled_rides']);
        $this->assertSame(1, $metrics['completed_rides']);
        $this->assertSame(0, $metrics['cancelled_rides']);
        $this->assertSame(1, $metrics['total_bookings']);
    }

    public function test_it_can_suspend_and_reactivate_a_user(): void
    {
        $service = new AdminService;
        $user = User::factory()->create();

        $service->suspendUser($user);

        $this->assertSame('suspended', $user->fresh()->account_status);
        $this->assertNotNull($user->fresh()->suspended_at);

        $service->activateUser($user->fresh());

        $this->assertSame('active', $user->fresh()->account_status);
        $this->assertNull($user->fresh()->suspended_at);
    }

    public function test_it_can_filter_users_by_status(): void
    {
        $service = new AdminService;

        User::factory()->count(2)->create(['account_status' => 'active']);
        User::factory()->create([
            'account_status' => 'suspended',
            'suspended_at' => now(),
        ]);

        $suspendedUsers = $service->listUsers('suspended');

        $this->assertCount(1, $suspendedUsers);
        $this->assertTrue($suspendedUsers->every(fn (User $user) => $user->account_status === 'suspended'));
    }

    public function test_it_can_moderate_a_ride_and_store_admin_note(): void
    {
        $service = new AdminService;
        $ride = $this->createRide();

        $updatedRide = $service->moderateRide($ride, 'cancelled', 'Fraudulent listing reported by users.');

        $this->assertSame('cancelled', $updatedRide->status);
        $this->assertSame('Fraudulent listing reported by users.', $updatedRide->admin_note);
    }

    public function test_it_rejects_unknown_moderation_statuses(): void
    {
        $service = new AdminService;
        $ride = $this->createRide();

        $this->expectException(InvalidArgumentException::class);

        $service->moderateRide($ride, 'archived');
    }

    private function createRide(): Ride
    {
        $driver = User::factory()->create();

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'BB123456',
            'cin_photo' => 'cin/driver-2.jpg',
            'cin_verified' => true,
            'avg_rating' => 4.5,
            'total_trips' => 6,
        ]);

        $departureCity = City::query()->create(['name' => 'Marrakech']);
        $arrivalCity = City::query()->create(['name' => 'Fes']);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Renault',
            'model' => 'Clio',
            'photo' => null,
        ]);

        return Ride::query()->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->addHours(6),
            'price_per_seat' => 95,
            'total_seats' => 3,
            'available_seats' => 3,
            'meeting_point' => 'Gare Marrakech',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ]);
    }
}
