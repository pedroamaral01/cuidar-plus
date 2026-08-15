<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this
            ->actingAs($usuario)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this
            ->actingAs($usuario)
            ->patch('/profile', [
                'nome' => 'Maria Teste',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $usuario->refresh();

        $this->assertSame('Maria Teste', $usuario->nome);
        $this->assertSame('test@example.com', $usuario->email);
        $this->assertNull($usuario->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this
            ->actingAs($usuario)
            ->patch('/profile', [
                'nome' => 'Maria Teste',
                'email' => $usuario->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($usuario->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this
            ->actingAs($usuario)
            ->delete('/profile', [
                'password' => 'senha1234',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($usuario->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this
            ->actingAs($usuario)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect('/profile');

        $this->assertNotNull($usuario->fresh());
    }
}
