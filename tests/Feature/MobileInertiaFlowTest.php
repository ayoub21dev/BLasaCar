<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\DriverProfile;
use App\Models\Ride;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MobileInertiaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_guest_pages_render_with_mobile_components(): void
    {
        $this->withoutVite();

        [$ride] = $this->createPublicRide();

        $this->get(route('mobile.home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mobile/Home', false)
                ->where('featuredRides', fn ($rides) => collect($rides)->pluck('id')->contains($ride->id)));

        $this->get(route('mobile.login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Mobile/Login', false));

        $this->get(route('mobile.signup'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Mobile/Signup', false));

        $this->get(route('mobile.rides.show', $ride))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mobile/RideDetails', false)
                ->where('ride.id', $ride->id));
    }

    public function test_mobile_login_redirects_back_to_mobile_flow(): void
    {
        $this->withoutVite();

        $user = User::factory()->traveler()->create([
            'email' => 'mobile-traveler@example.test',
            'password_hash' => 'password',
        ]);

        $this->from(route('mobile.login'))
            ->post(route('login.store'), [
                'email' => 'mobile-traveler@example.test',
                'password' => 'password',
                'remember' => true,
                'redirect_to' => '/mobile/home',
            ])
            ->assertRedirect('/mobile/home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_mobile_booking_redirects_to_mobile_trips(): void
    {
        $this->withoutVite();

        [$ride] = $this->createPublicRide();
        $traveler = User::factory()->traveler()->create();

        $this->actingAs($traveler)
            ->from(route('mobile.rides.show', $ride))
            ->post(route('rides.book', $ride), [
                'seats' => 1,
                'redirect_to' => '/mobile/trips',
            ])
            ->assertRedirect('/mobile/trips')
            ->assertSessionHas('status', 'Your seat request has been sent.');

        $this->assertDatabaseHas('bookings', [
            'ride_id' => $ride->id,
            'traveler_id' => $traveler->id,
            'seats_reserved' => 1,
            'status' => 'pending',
        ]);
    }

    /**
     * @return array{Ride, User}
     */
    private function createPublicRide(array $rideOverrides = [], array $driverOverrides = []): array
    {
        $driver = User::factory()->driver()->create([
            'first_name' => 'Mobile',
            'last_name' => 'Driver',
            ...$driverOverrides,
        ]);

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => fake()->unique()->bothify('??######'),
            'cin_photo' => null,
            'cin_front_photo' => null,
            'cin_back_photo' => null,
            'cin_verified' => true,
            'avg_rating' => 4.8,
            'total_trips' => 18,
        ]);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
            'photo' => null,
        ]);

        $departureCity = City::query()->create(['name' => 'Mobile Casablanca']);
        $arrivalCity = City::query()->create(['name' => 'Mobile Rabat']);

        $ride = Ride::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 80,
            'total_seats' => 4,
            'available_seats' => 3,
            'meeting_point' => 'Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
            ...$rideOverrides,
        ]);

        return [$ride, $driver];
    }
}
