<?php

declare(strict_types=1);

namespace Tests\Feature\Autenticacao;

use App\Models\Usuario;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RecuperacaoDeSenhaTest extends TestCase
{
    use RefreshDatabase;

    public function test_tela_de_solicitacao_e_exibida(): void
    {
        $this->get(route('senha.solicitar'))->assertOk();
    }

    public function test_link_de_recuperacao_e_enviado(): void
    {
        Notification::fake();
        $paciente = Usuario::factory()->paciente()->create();

        $this->post(route('senha.enviar-link'), ['email' => $paciente->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($paciente, ResetPassword::class);
    }

    public function test_senha_e_redefinida_com_token_valido(): void
    {
        Notification::fake();
        $paciente = Usuario::factory()->paciente()->create();

        $this->post(route('senha.enviar-link'), ['email' => $paciente->email]);

        Notification::assertSentTo($paciente, ResetPassword::class, function (object $notificacao) use ($paciente): bool {
            $this->post(route('senha.atualizar'), [
                'token' => $notificacao->token,
                'email' => $paciente->email,
                'password' => 'nova-senha-1234',
                'password_confirmation' => 'nova-senha-1234',
            ])->assertRedirect(route('login'));

            return true;
        });

        $this->assertTrue(
            Hash::check('nova-senha-1234', $paciente->refresh()->senha),
            'A nova senha precisa ser gravada na coluna senha, com hash.',
        );
    }

    public function test_paciente_logado_troca_a_propria_senha(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->put(route('senha.trocar'), [
                'current_password' => 'senha1234',
                'password' => 'nova-senha-1234',
                'password_confirmation' => 'nova-senha-1234',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('nova-senha-1234', $paciente->refresh()->senha));
    }

    public function test_troca_de_senha_exige_a_senha_atual_correta(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->put(route('senha.trocar'), [
                'current_password' => 'senha-errada',
                'password' => 'nova-senha-1234',
                'password_confirmation' => 'nova-senha-1234',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('senha1234', $paciente->refresh()->senha));
    }
}
