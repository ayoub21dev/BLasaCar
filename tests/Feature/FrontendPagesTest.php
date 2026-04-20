<?php

namespace Tests\Feature;

use App\Models\Ride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_key_frontend_pages_render_with_seeded_data(): void
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

        $this->get(route('dashboards.admin'))
            ->assertOk()
            ->assertSee('Platform overview', false);

        $this->get(route('dashboards.driver'))
            ->assertOk()
            ->assertSee('Driver workspace', false);

        $this->get(route('dashboards.traveler'))
            ->assertOk()
            ->assertSee('Traveler workspace', false);
    }
}
