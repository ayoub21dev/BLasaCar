<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_settings(): void
    {
        $this->get(route('account.settings.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_settings(): void
    {
        $this->withoutVite();

        $user = User::factory()->traveler()->create();

        $this->actingAs($user)
            ->get(route('account.settings.edit'))
            ->assertOk()
            ->assertSee('Account preferences', false)
            ->assertSee('Profile details', false)
            ->assertSee('Password', false);
    }

    public function test_authenticated_user_can_update_profile_details(): void
    {
        $user = User::factory()->traveler()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'email' => 'old@example.test',
            'phone' => '0600000000',
        ]);

        $this->actingAs($user)
            ->patch(route('account.settings.profile.update'), [
                'first_name' => 'Nora',
                'last_name' => 'Amrani',
                'email' => 'nora@example.test',
                'phone' => '0611111111',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Nora',
            'last_name' => 'Amrani',
            'email' => 'nora@example.test',
            'phone' => '0611111111',
        ]);
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = User::factory()->traveler()->create([
            'password_hash' => 'password',
        ]);

        $this->actingAs($user)
            ->patch(route('account.settings.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password_hash));
    }

    public function test_password_update_rejects_wrong_current_password(): void
    {
        $user = User::factory()->traveler()->create([
            'password_hash' => 'password',
        ]);

        $this->actingAs($user)
            ->from(route('account.settings.edit'))
            ->patch(route('account.settings.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('account.settings.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password_hash));
    }
}
