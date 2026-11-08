<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_user_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'password' => 'Password123!',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_sign_in(): void
    {
        $user = User::factory()->create([
            'password' => 'Password123!',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
