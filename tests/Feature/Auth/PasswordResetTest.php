<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $usuario = Usuario::factory()->create();

        $this->post('/forgot-password', ['email' => $usuario->email]);

        Notification::assertSentTo($usuario, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $usuario = Usuario::factory()->create();

        $this->post('/forgot-password', ['email' => $usuario->email]);

        Notification::assertSentTo($usuario, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $usuario = Usuario::factory()->create();

        $this->post('/forgot-password', ['email' => $usuario->email]);

        Notification::assertSentTo($usuario, ResetPassword::class, function ($notification) use ($usuario) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $usuario->email,
                'password' => 'senha1234',
                'password_confirmation' => 'senha1234',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
