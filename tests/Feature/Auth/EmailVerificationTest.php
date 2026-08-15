<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $usuario = Usuario::factory()->naoVerificado()->create();

        $response = $this->actingAs($usuario)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $usuario = Usuario::factory()->naoVerificado()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $usuario->id, 'hash' => sha1($usuario->email)]
        );

        $response = $this->actingAs($usuario)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($usuario->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $usuario = Usuario::factory()->naoVerificado()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $usuario->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($usuario)->get($verificationUrl);

        $this->assertFalse($usuario->fresh()->hasVerifiedEmail());
    }
}
