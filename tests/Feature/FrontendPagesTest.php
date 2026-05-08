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

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_frontend_pages_render_for_guests(): void
    {
        $this->withoutVite();
        $this->seed();

        $ride = Ride::query()
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now())
            ->whereHas('driverProfile.user', fn ($query) => $query->where('account_status', 'active'))
            ->firstOrFail();

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Home', false));

        $this->get(route('rides.search'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Search', false));

        $this->get(route('rides.show', $ride))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('RideDetails', false));

        $this->get(route('rides.publish'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Publish', false));

        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login', false));

        $this->get(route('signup'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Signup', false));
    }

    public function test_public_ride_details_hide_driver_private_contact_fields(): void
    {
        $this->withoutVite();

        [$ride, $driver] = $this->createPublicRide();

        $this->get(route('rides.show', $ride))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('RideDetails', false)
                ->where('ride.driver.name', trim($driver->first_name.' '.$driver->last_name))
                ->missing('ride.driver.email')
                ->missing('ride.driver.phone')
                ->missing('ride.driver.role')
                ->missing('ride.driver.dashboard_route'));
    }

    public function test_guest_cannot_open_inactive_ride_details(): void
    {
        $this->withoutVite();

        [$ride] = $this->createPublicRide([
            'status' => 'cancelled',
        ]);

        $this->get(route('rides.show', $ride))
            ->assertNotFound();
    }

    public function test_dashboard_routes_redirect_guests_to_login(): void
    {
        $this->withoutVite();
        $this->seed();

        $this->get(route('dashboards.admin'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboards.admin.driver-verification'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboards.admin.users'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboards.admin.rides'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboards.driver'))
            ->assertRedirect(route('login'));

        $this->get(route('dashboards.traveler'))
            ->assertRedirect(route('login'));
    }

    public function test_city_dropdowns_include_expanded_morocco_city_list(): void
    {
        $this->withoutVite();
        $this->seed();

        $this->assertGreaterThanOrEqual(100, City::query()->count());

        foreach (['Laayoune', 'Dakhla', 'Chefchaouen', 'Ouarzazate', 'Nador'] as $city) {
            $this->get(route('home'))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Home', false)
                    ->where('cities', fn ($cities) => collect($cities)->pluck('name')->contains($city)));

            $this->get(route('rides.search'))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Search', false)
                    ->where('cities', fn ($cities) => collect($cities)->pluck('name')->contains($city)));

            $this->get(route('rides.publish'))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Publish', false)
                    ->where('cities', fn ($cities) => collect($cities)->pluck('name')->contains($city)));
        }
    }

    /**
     * @return array{Ride, User}
     */
    private function createPublicRide(array $rideOverrides = [], array $driverOverrides = []): array
    {
        $driver = User::factory()->driver()->create([
            'first_name' => 'Yassine',
            'last_name' => 'El Mansouri',
            'email' => 'private-driver@example.test',
            'phone' => '0600001111',
            ...$driverOverrides,
        ]);

        $driverProfile = DriverProfile::query()->create([
            'user_id' => $driver->id,
            'cin_number' => 'ZX987654',
            'cin_photo' => null,
            'cin_front_photo' => null,
            'cin_back_photo' => null,
            'cin_verified' => true,
            'avg_rating' => 4.7,
            'total_trips' => 12,
        ]);

        $vehicle = Vehicle::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'brand' => 'Dacia',
            'model' => 'Logan',
            'photo' => null,
        ]);

        $departureCity = City::query()->create(['name' => 'Test Casablanca']);
        $arrivalCity = City::query()->create(['name' => 'Test Rabat']);

        $ride = Ride::query()->create([
            'driver_profile_id' => $driverProfile->id,
            'vehicle_id' => $vehicle->id,
            'departure_city_id' => $departureCity->id,
            'arrival_city_id' => $arrivalCity->id,
            'departure_time' => now()->addDay(),
            'price_per_seat' => 75,
            'total_seats' => 4,
            'available_seats' => 3,
            'meeting_point' => 'Gare Casa Voyageurs',
            'notes' => null,
            'admin_note' => null,
            'status' => 'scheduled',
            ...$rideOverrides,
        ]);

        return [$ride, $driver];
    }
}
