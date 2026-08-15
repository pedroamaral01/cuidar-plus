<?php

declare(strict_types=1);

namespace Tests\Feature\Autenticacao;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * A área administrativa não pode ficar acessível só por conhecer a URL.
 */
class SeparacaoDePerfisTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_nao_acessa_a_area_do_paciente(): void
    {
        $this->get(route('paciente.inicio'))->assertRedirect(route('login'));
    }

    public function test_visitante_nao_acessa_a_area_administrativa(): void
    {
        $this->get(route('administracao.painel'))->assertRedirect(route('login'));
    }

    public function test_paciente_recebe_403_na_area_administrativa(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->get(route('administracao.painel'))
            ->assertForbidden();
    }

    public function test_administrador_e_redirecionado_ao_abrir_a_area_do_paciente(): void
    {
        $admin = Usuario::factory()->administrador()->create();

        $this->actingAs($admin)
            ->get(route('paciente.inicio'))
            ->assertRedirect(route('administracao.painel'));
    }

    public function test_paciente_ve_o_proprio_painel(): void
    {
        $paciente = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);

        $this->actingAs($paciente)
            ->get(route('paciente.inicio'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Inicio')
                ->where('painel.primeiroNome', 'Maria')
                ->has('painel.resumoDaSemana'),
            );
    }

    public function test_administrador_ve_o_painel_com_estatisticas(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        Usuario::factory()->count(2)->paciente()->create();

        $this->actingAs($admin)
            ->get(route('administracao.painel'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Administracao/Painel')
                ->where('estatisticas.pacientesAtivos', 2),
            );
    }

    public function test_props_compartilhadas_nunca_expoem_a_senha(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->get(route('paciente.inicio'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->has('auth.usuario', fn (AssertableInertia $usuario) => $usuario
                    ->hasAll(['id', 'nome', 'email', 'perfil', 'iniciais'])
                    ->missing('senha')
                    ->missing('password')
                    ->missing('remember_token'),
                ),
            );
    }
}
