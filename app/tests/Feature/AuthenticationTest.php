<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_login(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Should still be guest (login rejected)
        // Note: This test requires implementing is_active check in AuthenticatedSessionController
        $this->assertGuest();
    }

    public function test_google_oauth_redirect(): void
    {
        $response = $this->get(route('auth.google'));

        // Should redirect to Google
        $response->assertRedirect();
        $this->assertStringContainsString('google.com', $response->headers->get('Location') ?? '');
    }

    public function test_whatsapp_login_page_can_be_rendered(): void
    {
        // Enable WhatsApp login in config
        config(['peduliku.login_methods.whatsapp' => true]);

        $response = $this->get(route('auth.whatsapp'));

        $response->assertOk();
    }

    public function test_whatsapp_otp_requires_registered_number(): void
    {
        config(['peduliku.login_methods.whatsapp' => true]);

        $response = $this->post(route('auth.whatsapp.send'), [
            'whatsapp' => '081234567890',
        ]);

        $response->assertSessionHasErrors('whatsapp');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
