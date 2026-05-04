<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RideWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_publish_a_ride(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $departureAt = now()->addDays(2)->setTime(9, 30);

        $response = $this->actingAs($driver)->post(route('rides.publish.store'), [
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'departure_date' => $departureAt->format('Y-m-d'),
            'departure_time' => $departureAt->format('H:i'),
            'seats_offered' => 3,
            'price_per_seat' => 85,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => 'Small luggage only.',
        ]);

        $ride = Ride::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('rides.show', $ride));
        $this->assertDatabaseHas('rides', [
            'id' => $ride->id,
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'total_seats' => 3,
            'available_seats' => 3,
            'status' => 'scheduled',
        ]);
    }

    public function test_unverified_driver_cannot_publish_a_ride(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle(cinVerified: false);
        [$casablanca, $rabat] = $this->createRouteCities();
        $departureAt = now()->addDays(2)->setTime(9, 30);

        $this->actingAs($driver)->post(route('rides.publish.store'), [
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'departure_date' => $departureAt->format('Y-m-d'),
            'departure_time' => $departureAt->format('H:i'),
            'seats_offered' => 3,
            'price_per_seat' => 85,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => 'Small luggage only.',
        ])->assertForbidden();

        $this->assertDatabaseCount('rides', 0);
    }

    public function test_traveler_can_request_a_seat(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();

        $ride = Ride::query()->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 2,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $response->assertRedirect(route('dashboards.traveler'));

        $this->assertDatabaseHas('bookings', [
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
        ]);
        $this->assertSame(1, $ride->fresh()->available_seats);
    }

    public function test_traveler_cannot_request_a_past_ride(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();

        $ride = Ride::query()->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $casablanca->id,
            'arrival_city_id' => $rabat->id,
            'departure_time' => now()->subDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 2,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
        ]);

        $this->actingAs($traveler)
            ->from(route('rides.show', $ride))
            ->post(route('rides.book', $ride), ['seats' => 1])
            ->assertRedirect(route('rides.show', $ride))
            ->assertSessionHasErrors('seats');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertSame(2, $ride->fresh()->available_seats);
    }

    public function test_driver_can_accept_a_pending_booking_request(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();

        $this->actingAs($driver)
            ->from(route('dashboards.driver'))
            ->patch(route('bookings.confirm', $booking))
            ->assertRedirect(route('dashboards.driver'));

        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertSame(1, $ride->fresh()->available_seats);
    }

    public function test_driver_can_reject_a_pending_booking_request_and_restore_seats(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();
        $this->assertSame(1, $ride->fresh()->available_seats);

        $this->actingAs($driver)
            ->from(route('dashboards.driver'))
            ->patch(route('bookings.reject', $booking))
            ->assertRedirect(route('dashboards.driver'));

        $this->assertSame('rejected', $booking->fresh()->status);
        $this->assertSame(2, $ride->fresh()->available_seats);
    }

    public function test_traveler_can_cancel_an_active_booking_and_restore_seats(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();
        $this->assertSame(1, $ride->fresh()->available_seats);

        $this->actingAs($traveler)
            ->from(route('dashboards.traveler'))
            ->patch(route('bookings.cancel', $booking))
            ->assertRedirect(route('dashboards.traveler'));

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame(2, $ride->fresh()->available_seats);
    }

    public function test_traveler_cannot_cancel_another_travelers_booking(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $otherTraveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();

        $this->actingAs($otherTraveler)
            ->patch(route('bookings.cancel', $booking))
            ->assertForbidden();

        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertSame(1, $ride->fresh()->available_seats);
    }

    public function test_traveler_cannot_cancel_terminal_booking(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();
        $booking->update(['status' => 'completed']);

        $this->actingAs($traveler)
            ->from(route('dashboards.traveler'))
            ->patch(route('bookings.cancel', $booking))
            ->assertRedirect(route('dashboards.traveler'))
            ->assertSessionHasErrors('booking');

        $this->assertSame('completed', $booking->fresh()->status);
        $this->assertSame(1, $ride->fresh()->available_seats);
    }

    public function test_driver_cannot_handle_booking_for_another_drivers_ride(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$otherDriver] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createScheduledRide($driver, $vehicle, $casablanca, $rabat, availableSeats: 2);

        $this->actingAs($traveler)->post(route('rides.book', $ride), [
            'seats' => 1,
        ]);

        $booking = Booking::query()->where('ride_id', $ride->id)->firstOrFail();

        $this->actingAs($otherDriver)
            ->from(route('dashboards.driver'))
            ->patch(route('bookings.confirm', $booking))
            ->assertRedirect(route('dashboards.driver'))
            ->assertSessionHasErrors('booking');

        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertSame(1, $ride->fresh()->available_seats);
    }

    /**
     * @return array{0: User, 1: Vehicle}
     */
    private function createDriverWithVehicle(bool $cinVerified = true): array
    {
        $driver = User::factory()->driver()->create();

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => fake()->unique()->bothify('??######'),
            'cin_photo' => 'cin/driver.jpg',
            'cin_verified' => $cinVerified,
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

    private function createScheduledRide(User $driver, Vehicle $vehicle, City $departureCity, City $arrivalCity, int $availableSeats): Ride
    {
        return Ride::query()->create([
            'user_id' => $driver->id,
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
        ]);
    }
}
