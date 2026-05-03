<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_and_reach_admin_dashboard(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->post(route('login.store'), [
            'email' => 'admin@blassacar.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboards.admin'));
        $this->assertAuthenticated();
    }

    public function test_driver_can_log_in_and_reach_driver_dashboard(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->post(route('login.store'), [
            'email' => 'yassine.elmansouri@blassacar.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboards.driver'));
        $this->assertAuthenticated();
    }

    public function test_traveler_can_log_in_and_reach_traveler_dashboard(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->post(route('login.store'), [
            'email' => 'ayoub.rami@blassacar.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboards.traveler'));
        $this->assertAuthenticated();
    }

    public function test_suspended_account_cannot_log_in(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'imane.tazi@blassacar.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guest_can_create_a_traveler_account(): void
    {
        $response = $this->post(route('signup.store'), [
            'full_name' => 'Nora Amrani',
            'phone' => '0699999999',
            'email' => 'nora.amrani@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboards.traveler'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'first_name' => 'Nora',
            'last_name' => 'Amrani',
            'email' => 'nora.amrani@example.test',
            'phone' => '0699999999',
            'role' => User::ROLE_TRAVELER,
            'account_status' => 'active',
        ]);
    }

    public function test_role_middleware_redirects_users_to_their_own_dashboard(): void
    {
        $this->withoutVite();
        $this->seed();

        $traveler = User::query()
            ->where('email', 'ayoub.rami@blassacar.test')
            ->firstOrFail();

        $this->actingAs($traveler)
            ->get(route('dashboards.admin'))
            ->assertRedirect(route('dashboards.traveler'));
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->withoutVite();
        $this->seed();

        $user = User::query()
            ->where('email', 'admin@blassacar.test')
            ->firstOrFail();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
