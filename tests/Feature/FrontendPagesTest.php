<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Ride;
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

        $ride = Ride::query()->firstOrFail();

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
}
