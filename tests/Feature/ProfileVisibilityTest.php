<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Review;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProfileVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_traveler_can_view_driver_profile_reviews_without_private_fields(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create([
            'first_name' => 'Ayoub',
            'last_name' => 'Rami',
        ]);
        $ride = $this->createRide($driver, $vehicle, $casablanca, $rabat, 'completed');
        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'completed',
            'booked_at' => now()->subDay(),
        ]);

        Review::query()->create([
            'booking_id' => $booking->id,
            'traveler_id' => $traveler->id,
            'driver_profile_id' => $driver->driverProfile->id,
            'rating' => 5,
            'comment' => 'Clean car and clear meeting point.',
        ]);

        $this->actingAs($traveler)
            ->get(route('profiles.drivers.show', $driver->driverProfile))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profiles/Driver', false)
                ->where('driver.name', $driver->first_name.' '.$driver->last_name)
                ->where('profile.id', $driver->driverProfile->id)
                ->where('profile.cin_verified', true)
                ->missing('driver.phone')
                ->missing('driver.email')
                ->missing('profile.cin_number')
                ->has('reviews', 1)
                ->where('reviews.0.rating', 5)
                ->where('reviews.0.traveler.name', 'Ayoub Rami'));
    }

    public function test_driver_can_view_traveler_profile_for_their_booking_and_contact_stays_gated(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create([
            'phone' => '0655555555',
            'first_name' => 'Lina',
            'last_name' => 'Ouahbi',
        ]);
        $ride = $this->createRide($driver, $vehicle, $casablanca, $rabat);
        $booking = Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 2,
            'status' => 'pending',
            'booked_at' => now(),
        ]);

        $this->actingAs($driver)
            ->get(route('profiles.travelers.show', $traveler))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profiles/Traveler', false)
                ->where('traveler.name', 'Lina Ouahbi')
                ->missing('traveler.phone')
                ->where('stats.total_bookings', 1)
                ->where('bookings.0.can_view_contact', false)
                ->where('bookings.0.traveler_contact', null));

        $booking->update(['status' => 'confirmed']);

        $this->actingAs($driver)
            ->get(route('profiles.travelers.show', $traveler))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profiles/Traveler', false)
                ->where('bookings.0.can_view_contact', true)
                ->where('bookings.0.traveler_contact.phone', '0655555555')
                ->where('bookings.0.traveler_contact.whatsapp_url', 'https://wa.me/212655555555'));
    }

    public function test_driver_cannot_view_unrelated_traveler_profile(): void
    {
        [$driver, $vehicle] = $this->createDriverWithVehicle();
        [$otherDriver] = $this->createDriverWithVehicle();
        [$casablanca, $rabat] = $this->createRouteCities();
        $traveler = User::factory()->traveler()->create();
        $ride = $this->createRide($driver, $vehicle, $casablanca, $rabat);

        Booking::query()->create([
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
            'booked_at' => now(),
        ]);

        $this->actingAs($otherDriver)
            ->get(route('profiles.travelers.show', $traveler))
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: Vehicle}
     */
    private function createDriverWithVehicle(): array
    {
        $driver = User::factory()->driver()->create();

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

    private function createRide(User $driver, Vehicle $vehicle, City $departureCity, City $arrivalCity, string $status = 'scheduled'): Ride
    {
        return Ride::query()->create([
            'driver_profile_id' => $driver->driverProfile->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => $status === 'scheduled' ? now()->addDay() : now()->subDay(),
            'price_per_seat' => 70,
            'total_seats' => 4,
            'available_seats' => 2,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => $status,
        ]);
    }
}
