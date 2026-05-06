<?php

namespace Tests\Unit\PublicApp;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\PublicApp\PublicRideService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class PublicRideServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_only_bookable_rides_for_a_route_and_date(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();

        $bookableRide = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
        );

        $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 10:00',
            overrides: ['available_seats' => 0],
        );

        $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 12:00',
            overrides: ['status' => 'cancelled'],
        );

        $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+2 days 09:00',
        );

        $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '-1 day 09:00',
        );

        $results = $service->searchRides(
            $casablanca->id,
            $rabat->id,
            now()->addDay(),
        );

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($bookableRide));
    }

    public function test_it_rejects_past_rides_for_booking(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '-1 day 08:00',
        );
        $traveler = User::factory()->traveler()->create();

        $this->expectException(RuntimeException::class);

        $service->requestSeat($traveler, $ride, 1);
    }

    public function test_it_creates_a_pending_booking_and_decrements_available_seats(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 3],
        );
        $traveler = User::factory()->create();

        $booking = $service->requestSeat($traveler, $ride, 2);

        $this->assertSame('pending', $booking->status);
        $this->assertSame(2, $booking->seats_reserved);
        $this->assertSame(1, $ride->fresh()->available_seats);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $ride->user_id,
            'type' => 'booking_created',
            'channel' => 'in_app',
            'related_entity_type' => Booking::class,
            'related_entity_id' => $booking->id,
            'is_read' => false,
        ]);
    }

    public function test_it_rejects_duplicate_active_booking_requests_for_the_same_ride(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 3],
        );
        $traveler = User::factory()->create();

        $service->requestSeat($traveler, $ride, 1);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('already have an active booking');

        $service->requestSeat($traveler, $ride->fresh(), 1);
    }

    public function test_it_allows_a_new_booking_after_previous_request_is_not_active(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 3],
        );
        $traveler = User::factory()->create();

        $booking = $service->requestSeat($traveler, $ride, 1);
        $booking->update(['status' => 'cancelled']);
        $ride->increment('available_seats');

        $newBooking = $service->requestSeat($traveler, $ride->fresh(), 1);

        $this->assertSame('pending', $newBooking->status);
        $this->assertDatabaseCount('bookings', 2);
        $this->assertSame(2, $ride->fresh()->available_seats);
    }

    public function test_it_rejects_invalid_booking_requests(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 1],
        );
        $traveler = User::factory()->create();

        $this->expectException(RuntimeException::class);

        $service->requestSeat($traveler, $ride, 2);
    }

    public function test_it_rejects_zero_seat_requests(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
        );
        $traveler = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        $service->requestSeat($traveler, $ride, 0);
    }

    public function test_it_lists_booking_statuses_for_a_traveler(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->create();

        $rideA = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
        );

        $rideB = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+2 days 08:00',
        );

        Booking::query()->create([
            'ride_id' => $rideA->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
            'booked_at' => now()->subHour(),
        ]);

        Booking::query()->create([
            'ride_id' => $rideB->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'confirmed',
            'booked_at' => now(),
        ]);

        $statuses = $service->listBookingStatuses($traveler);

        $this->assertCount(2, $statuses);
        $this->assertSame('confirmed', $statuses->first()->status);
        $this->assertSame('pending', $statuses->last()->status);
    }

    public function test_it_cancels_a_booking_and_restores_available_seats(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 1],
        );
        $traveler = User::factory()->create();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 2,
            'status' => 'confirmed',
            'booked_at' => now(),
        ]);

        $cancelledBooking = $service->cancelBooking($booking);

        $this->assertSame('cancelled', $cancelledBooking->status);
        $this->assertSame(3, $ride->fresh()->available_seats);
    }

    public function test_it_rejects_cancellation_for_terminal_booking_statuses(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
        );
        $traveler = User::factory()->create();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'completed',
            'booked_at' => now(),
        ]);

        $this->expectException(RuntimeException::class);

        $service->cancelBooking($booking);
    }

    public function test_it_notifies_the_traveler_when_a_booking_is_confirmed(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
        );
        $traveler = User::factory()->create();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
            'booked_at' => now(),
        ]);

        $service->confirmBooking($ride->user, $booking);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $traveler->id,
            'type' => 'booking_confirmed',
            'channel' => 'in_app',
            'related_entity_type' => Booking::class,
            'related_entity_id' => $booking->id,
            'is_read' => false,
        ]);
    }

    public function test_it_notifies_the_traveler_when_a_booking_is_rejected(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 2],
        );
        $traveler = User::factory()->create();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
            'booked_at' => now(),
        ]);

        $service->rejectBooking($ride->user, $booking);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $traveler->id,
            'type' => 'booking_rejected',
            'channel' => 'in_app',
            'related_entity_type' => Booking::class,
            'related_entity_id' => $booking->id,
            'is_read' => false,
        ]);
    }

    public function test_it_notifies_the_driver_when_a_traveler_cancels_a_booking(): void
    {
        $service = $this->service();
        [$casablanca, $rabat] = $this->createRouteCities();
        $ride = $this->createRide(
            departureCity: $casablanca,
            arrivalCity: $rabat,
            departureTimeModifier: '+1 day 08:00',
            overrides: ['available_seats' => 2],
        );
        $traveler = User::factory()->create();

        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'confirmed',
            'booked_at' => now(),
        ]);

        $service->cancelBooking($booking);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $ride->user_id,
            'type' => 'booking_cancelled',
            'channel' => 'in_app',
            'related_entity_type' => Booking::class,
            'related_entity_id' => $booking->id,
            'is_read' => false,
        ]);
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

    private function service(): PublicRideService
    {
        return app(PublicRideService::class);
    }

    private function createRide(City $departureCity, City $arrivalCity, string $departureTimeModifier, array $overrides = []): Ride
    {
        $driver = User::factory()->create($overrides['driver'] ?? []);

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => fake()->unique()->bothify('??######'),
            'cin_photo' => 'cin/driver.jpg',
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

        unset($overrides['driver']);

        return Ride::query()->create(array_merge([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->modify($departureTimeModifier),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 3,
            'meeting_point' => 'Central station',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ], $overrides));
    }
}
