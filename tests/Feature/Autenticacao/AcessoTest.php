<?php

declare(strict_types=1);

namespace Tests\Feature\Autenticacao;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AcessoTest extends TestCase
{
    use RefreshDatabase;

    public function test_tela_de_acesso_e_exibida(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Autenticacao/Acesso')
                ->where('podeRedefinirSenha', true),
            );
    }

    public function test_paciente_entra_com_credenciais_corretas(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $resposta = $this->post(route('login.entrar'), [
            'email' => $paciente->email,
            'password' => 'senha1234',
        ]);

        $this->assertAuthenticatedAs($paciente);
        $resposta->assertRedirect(route('paciente.inicio'));
    }

    public function test_administrador_entra_e_vai_para_a_area_administrativa(): void
    {
        $admin = Usuario::factory()->administrador()->create();

        $resposta = $this->post(route('login.entrar'), [
            'email' => $admin->email,
            'password' => 'senha1234',
        ]);

        $this->assertAuthenticatedAs($admin);
        $resposta->assertRedirect(route('administracao.painel'));
    }

    public function test_senha_incorreta_nao_autentica(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->post(route('login.entrar'), [
            'email' => $paciente->email,
            'password' => 'senha-errada',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_mensagem_de_erro_nao_revela_se_o_email_existe(): void
    {
        Usuario::factory()->paciente()->create(['email' => 'existe@exemplo.com']);

        // A mesma mensagem exata nos dois casos: não dá para descobrir quais
        // e-mails existem no sistema testando o formulário de acesso.
        $mensagemGenerica = 'E-mail ou senha incorretos.';

        $this->post(route('login.entrar'), [
            'email' => 'existe@exemplo.com',
            'password' => 'senha-errada',
        ])->assertSessionHasErrors(['email' => $mensagemGenerica]);

        $this->flushSession();

        $this->post(route('login.entrar'), [
            'email' => 'naoexiste@exemplo.com',
            'password' => 'senha-errada',
        ])->assertSessionHasErrors(['email' => $mensagemGenerica]);
    }

    public function test_login_tem_limite_de_tentativas(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        RateLimiter::clear(mb_strtolower($paciente->email).'|127.0.0.1');

        for ($tentativa = 1; $tentativa <= 6; $tentativa++) {
            $this->post(route('login.entrar'), [
                'email' => $paciente->email,
                'password' => 'senha-errada',
            ]);
        }

        // A sétima tentativa é barrada mesmo com a senha correta.
        $this->post(route('login.entrar'), [
            'email' => $paciente->email,
            'password' => 'senha1234',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_autenticado_consegue_sair(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_visitante_e_levado_ao_acesso_ao_abrir_a_raiz(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_paciente_autenticado_e_levado_ao_inicio_ao_abrir_a_raiz(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->get('/')
            ->assertRedirect(route('paciente.inicio'));
    }
}
