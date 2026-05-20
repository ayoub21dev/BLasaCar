<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\MobileApiToken;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_login_issues_a_bearer_token_for_active_users(): void
    {
        $user = User::factory()->traveler()->create([
            'email' => 'traveler@example.test',
            'password_hash' => 'secret-password',
        ]);

        $response = $this->postJson('/api/mobile/login', [
            'email' => 'traveler@example.test',
            'password' => 'secret-password',
            'device_name' => 'Pixel test',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', 'traveler@example.test')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'expires_at',
                    'user',
                ],
            ]);

        $this->assertDatabaseHas('mobile_api_tokens', [
            'user_id' => $user->id,
            'name' => 'Pixel test',
        ]);

        $this->withToken($response->json('data.token'))
            ->getJson('/api/mobile/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_mobile_login_rejects_suspended_users(): void
    {
        User::factory()->traveler()->create([
            'email' => 'suspended@example.test',
            'password_hash' => 'secret-password',
            'account_status' => 'suspended',
        ]);

        $this->postJson('/api/mobile/login', [
            'email' => 'suspended@example.test',
            'password' => 'secret-password',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'The provided credentials do not match an active account.');
    }

    public function test_mobile_rides_index_returns_only_bookable_rides(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();

        $visibleRide = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);
        $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2, overrides: [
            'departure_time' => now()->subDay(),
        ]);

        [$suspendedDriver, $suspendedVehicle] = $this->createDriverWithVehicle([
            'account_status' => 'suspended',
        ]);
        $this->createScheduledRide($suspendedDriver, $suspendedVehicle, $casablanca, $rabat, availableSeats: 2);

        $this->getJson('/api/mobile/rides')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visibleRide->id)
            ->assertJsonPath('meta.count', 1);
    }

    public function test_mobile_traveler_can_book_and_cancel_a_ride(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);
        $traveler = User::factory()->traveler()->create();
        [, $plainTextToken] = MobileApiToken::issueFor($traveler, 'test device');

        $bookResponse = $this->withToken($plainTextToken)
            ->postJson("/api/mobile/rides/{$ride->id}/book", [
                'seats' => 1,
            ]);

        $bookResponse
            ->assertCreated()
            ->assertJsonPath('data.ride_id', $ride->id)
            ->assertJsonPath('data.status', 'pending');

        $booking = Booking::query()->where('traveler_id', $traveler->id)->firstOrFail();

        $this->assertSame(1, $ride->fresh()->available_seats);

        $this->withToken($plainTextToken)
            ->getJson('/api/mobile/bookings')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $booking->id);

        $this->withToken($plainTextToken)
            ->patchJson("/api/mobile/bookings/{$booking->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame(2, $ride->fresh()->available_seats);
    }

    /**
     * @param  array<string, mixed>  $userOverrides
     * @return array{0: User, 1: Vehicle}
     */
    private function createDriverWithVehicle(array $userOverrides = []): array
    {
        $driver = User::factory()->driver()->create($userOverrides);

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => fake()->unique()->bothify('??######'),
            'cin_photo' => null,
            'cin_front_photo' => null,
            'cin_back_photo' => null,
            'cin_verified' => true,
            'avg_rating' => 4.5,
            'total_trips' => 8,
        ]);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
            'photo' => null,
        ]);

        return [$driver, $vehicle];
    }

    /**
     * @return array{0: City, 1: City}
     */
    private function createRouteCities(): array
    {
        return [
            City::query()->create(['name' => 'Casablanca']),
            City::query()->create(['name' => 'Rabat']),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createScheduledRide(
        User $driver,
        Vehicle $vehicle,
        City $departureCity,
        City $arrivalCity,
        int $availableSeats,
        array $overrides = [],
    ): Ride {
        return Ride::query()->create([
            'driver_profile_id' => $driver->driverProfile->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => $availableSeats,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
            ...$overrides,
        ]);
    }
}
