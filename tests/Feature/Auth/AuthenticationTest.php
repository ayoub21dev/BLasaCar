<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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

    public function test_suspended_existing_session_is_logged_out_from_protected_pages(): void
    {
        $traveler = User::factory()->traveler()->create([
            'account_status' => 'active',
            'email' => 'active-session@example.test',
            'password_hash' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'active-session@example.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboards.traveler'));

        $this->assertAuthenticated();

        User::query()
            ->whereKey($traveler->id)
            ->update([
                'account_status' => 'suspended',
                'suspended_at' => now(),
            ]);
        auth()->forgetGuards();

        $this->get(route('dashboards.traveler'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Your account is suspended. Contact support if this looks wrong.');

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

    public function test_admin_can_open_each_admin_dashboard_section(): void
    {
        $this->withoutVite();
        $this->seed();

        $admin = User::query()
            ->where('email', 'admin@blassacar.test')
            ->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboards.admin'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboards/Admin', false)
                ->where('section', 'overview'));

        $this->actingAs($admin)
            ->get(route('dashboards.admin.driver-verification'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboards/Admin', false)
                ->where('section', 'driver-verification'));

        $this->actingAs($admin)
            ->get(route('dashboards.admin.users'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboards/Admin', false)
                ->where('section', 'users'));

        $this->actingAs($admin)
            ->get(route('dashboards.admin.rides'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboards/Admin', false)
                ->where('section', 'rides'));
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
