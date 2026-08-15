<?php

declare(strict_types=1);

namespace Tests\Feature\Administracao;

use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CrudDeOrientacaoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Dispositivo $dispositivo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->administrador()->create();
        $this->dispositivo = Dispositivo::factory()->create(['nome' => 'Colostomia']);
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosValidos(array $sobrescrever = []): array
    {
        return [
            'dispositivo_id' => $this->dispositivo->id,
            'titulo' => 'Colostomia',
            'subtitulo' => 'Aprenda como realizar os cuidados corretamente.',
            'publicada' => true,
            'abas' => [
                [
                    'nome' => 'Troca da bolsa',
                    'passos' => ['Reúna o material.', 'Limpe a pele.', 'Fixe a nova bolsa.'],
                ],
                [
                    'nome' => 'Higiene',
                    'passos' => ['Lave as mãos antes e depois.'],
                ],
            ],
            ...$sobrescrever,
        ];
    }

    public function test_cadastro_cria_orientacao_com_abas_e_passos_na_ordem(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos())
            ->assertRedirect(route('administracao.orientacoes.index'));

        $orientacao = Orientacao::with('tiposDeCuidado.passos')->first();

        $this->assertNotNull($orientacao);
        $this->assertSame(['Troca da bolsa', 'Higiene'], $orientacao->tiposDeCuidado->pluck('nome')->all());
        $this->assertSame(
            ['Reúna o material.', 'Limpe a pele.', 'Fixe a nova bolsa.'],
            $orientacao->tiposDeCuidado->first()->passos->pluck('descricao')->all(),
        );
    }

    public function test_orientacao_precisa_de_ao_menos_uma_aba(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos(['abas' => []]))
            ->assertSessionHasErrors('abas');

        $this->assertDatabaseCount('orientacoes', 0);
    }

    public function test_aba_precisa_de_ao_menos_um_passo(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos([
                'abas' => [['nome' => 'Troca', 'passos' => []]],
            ]))
            ->assertSessionHasErrors('abas.0.passos');
    }

    public function test_passo_em_branco_e_recusado(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos([
                'abas' => [['nome' => 'Troca', 'passos' => ['Primeiro passo', '']]],
            ]))
            ->assertSessionHasErrors('abas.0.passos.1');
    }

    public function test_edicao_substitui_abas_e_passos_sem_deixar_orfaos(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos());

        $orientacao = Orientacao::first();

        $this->actingAs($this->admin)
            ->put(route('administracao.orientacoes.update', $orientacao), $this->dadosValidos([
                'abas' => [['nome' => 'Só uma aba agora', 'passos' => ['Passo único.']]],
            ]))
            ->assertRedirect(route('administracao.orientacoes.index'));

        $this->assertDatabaseCount('tipos_de_cuidado', 1);
        $this->assertDatabaseCount('passos_de_orientacao', 1);
    }

    public function test_formulario_de_edicao_devolve_as_abas_montadas(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos());

        $orientacao = Orientacao::first();

        $this->actingAs($this->admin)
            ->get(route('administracao.orientacoes.edit', $orientacao))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Administracao/Orientacoes/Formulario')
                ->has('orientacao.abas', 2)
                ->where('orientacao.abas.0.nome', 'Troca da bolsa')
                ->has('orientacao.abas.0.passos', 3),
            );
    }

    public function test_exclusao_remove_abas_e_passos_em_cascata(): void
    {
        $this->actingAs($this->admin)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos());

        $orientacao = Orientacao::first();

        $this->actingAs($this->admin)
            ->delete(route('administracao.orientacoes.destroy', $orientacao))
            ->assertRedirect(route('administracao.orientacoes.index'));

        $this->assertDatabaseCount('orientacoes', 0);
        $this->assertDatabaseCount('tipos_de_cuidado', 0);
        $this->assertDatabaseCount('passos_de_orientacao', 0);
    }

    public function test_paciente_nao_cadastra_orientacao(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->post(route('administracao.orientacoes.store'), $this->dadosValidos())
            ->assertForbidden();

        $this->assertDatabaseCount('orientacoes', 0);
    }
}
