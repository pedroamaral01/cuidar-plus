<?php

declare(strict_types=1);

namespace Tests\Feature\Paciente;

use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\PassoDeOrientacao;
use App\Models\SinalDeAlerta;
use App\Models\TipoDeCuidado;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TelasDoPacienteTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $maria;

    private Dispositivo $colostomia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->colostomia = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $this->maria = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);
        $this->maria->dispositivos()->attach($this->colostomia, ['ativo' => true]);
    }

    private function orientacaoComPassos(Dispositivo $dispositivo): Orientacao
    {
        $orientacao = Orientacao::factory()->create([
            'dispositivo_id' => $dispositivo->id,
            'titulo' => 'Cuidados diários',
        ]);

        $aba = TipoDeCuidado::create([
            'orientacao_id' => $orientacao->id,
            'nome' => 'Troca da bolsa',
            'ordem' => 0,
        ]);

        PassoDeOrientacao::create([
            'tipo_de_cuidado_id' => $aba->id,
            'descricao' => 'Reúna o material.',
            'ordem' => 0,
        ]);

        return $orientacao;
    }

    // ------------------------------------------------------------------
    // Meu dispositivo
    // ------------------------------------------------------------------

    public function test_paciente_ve_o_proprio_dispositivo_selecionado(): void
    {
        $this->actingAs($this->maria)
            ->get(route('paciente.dispositivo'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Dispositivo')
                ->where('dispositivoAtualId', $this->colostomia->id),
            );
    }

    public function test_paciente_troca_de_dispositivo(): void
    {
        $sonda = Dispositivo::factory()->create(['nome' => 'Sonda vesical']);

        $this->actingAs($this->maria)
            ->put(route('paciente.dispositivo.definir'), ['dispositivo_id' => $sonda->id])
            ->assertSessionHasNoErrors();

        $this->assertSame('Sonda vesical', $this->maria->refresh()->dispositivoAtual()?->nome);
    }

    public function test_paciente_nao_escolhe_dispositivo_inativo(): void
    {
        $inativo = Dispositivo::factory()->inativo()->create();

        $this->actingAs($this->maria)
            ->put(route('paciente.dispositivo.definir'), ['dispositivo_id' => $inativo->id])
            ->assertSessionHasErrors('dispositivo_id');
    }

    // ------------------------------------------------------------------
    // Orientações
    // ------------------------------------------------------------------

    public function test_lista_de_orientacoes_traz_apenas_as_do_dispositivo_do_paciente(): void
    {
        $this->orientacaoComPassos($this->colostomia);
        $sonda = Dispositivo::factory()->create();
        Orientacao::factory()->create(['dispositivo_id' => $sonda->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.orientacoes.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Orientacoes/Index')
                ->has('orientacoes', 1)
                ->where('orientacoes.0.titulo', 'Cuidados diários'),
            );
    }

    public function test_detalhe_da_orientacao_traz_abas_e_passo_a_passo(): void
    {
        $orientacao = $this->orientacaoComPassos($this->colostomia);

        $this->actingAs($this->maria)
            ->get(route('paciente.orientacoes.show', $orientacao))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Orientacoes/Detalhe')
                ->has('orientacao.abas', 1)
                ->where('orientacao.abas.0.nome', 'Troca da bolsa')
                ->where('orientacao.abas.0.passos.0', 'Reúna o material.'),
            );
    }

    public function test_paciente_nao_abre_orientacao_de_outro_dispositivo(): void
    {
        $sonda = Dispositivo::factory()->create();
        $orientacaoDaSonda = $this->orientacaoComPassos($sonda);

        $this->actingAs($this->maria)
            ->get(route('paciente.orientacoes.show', $orientacaoDaSonda))
            ->assertNotFound();
    }

    public function test_paciente_nao_abre_orientacao_despublicada(): void
    {
        $orientacao = $this->orientacaoComPassos($this->colostomia);
        $orientacao->update(['publicada' => false]);

        $this->actingAs($this->maria)
            ->get(route('paciente.orientacoes.show', $orientacao))
            ->assertNotFound();
    }

    // ------------------------------------------------------------------
    // Sinais de alerta
    // ------------------------------------------------------------------

    public function test_sinais_de_alerta_misturam_gerais_e_do_dispositivo_ordenados_por_gravidade(): void
    {
        SinalDeAlerta::factory()->geral()->create(['nome' => 'Febre', 'gravidade' => 'alta']);
        SinalDeAlerta::factory()->create([
            'dispositivo_id' => $this->colostomia->id,
            'nome' => 'Vermelhidão',
            'gravidade' => 'media',
        ]);
        $outro = Dispositivo::factory()->create();
        SinalDeAlerta::factory()->create(['dispositivo_id' => $outro->id, 'nome' => 'Urina turva']);

        $this->actingAs($this->maria)
            ->get(route('paciente.alertas'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Alertas')
                ->has('sinais', 2)
                ->where('sinais.0.nome', 'Febre')
                ->where('sinais.1.nome', 'Vermelhidão'),
            );
    }
}
