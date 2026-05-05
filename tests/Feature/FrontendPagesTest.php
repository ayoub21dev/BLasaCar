<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Ride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_frontend_pages_render_for_guests(): void
    {
        $this->withoutVite();
        $this->seed();

        $ride = Ride::query()->firstOrFail();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Travel between cities', false);

        $this->get(route('rides.search'))
            ->assertOk()
            ->assertSee('Find a route that fits your next trip.', false);

        $this->get(route('rides.show', $ride))
            ->assertOk()
            ->assertSee('Ride details', false);

        $this->get(route('rides.publish'))
            ->assertOk()
            ->assertSee('Publish a ride', false);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Welcome back', false);

        $this->get(route('signup'))
            ->assertOk()
            ->assertSee('Create an account', false);
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
                ->assertSee('Search city', false)
                ->assertSee($city, false);

            $this->get(route('rides.search'))
                ->assertOk()
                ->assertSee('Search city', false)
                ->assertSee($city, false);

            $this->get(route('rides.publish'))
                ->assertOk()
                ->assertSee('Search city', false)
                ->assertSee($city, false);
        }
    }
}
