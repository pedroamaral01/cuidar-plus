<?php

declare(strict_types=1);

namespace Tests\Feature\Paciente;

use App\Enums\TipoDeLembrete;
use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LembretesEDiarioTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $maria;

    private Usuario $joao;

    protected function setUp(): void
    {
        parent::setUp();

        $this->maria = Usuario::factory()->paciente()->create(['nome' => 'Maria Aparecida']);
        $this->joao = Usuario::factory()->paciente()->create(['nome' => 'João Pereira']);
    }

    // ------------------------------------------------------------------
    // Lembretes
    // ------------------------------------------------------------------

    public function test_agenda_mostra_a_semana_e_os_lembretes_do_paciente(): void
    {
        Lembrete::factory()->noHorario('08:00:00')->create([
            'usuario_id' => $this->maria->id,
            'titulo' => 'Trocar bolsa',
        ]);
        Lembrete::factory()->create(['usuario_id' => $this->joao->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.lembretes.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Lembretes')
                ->has('agenda.semana', 7)
                ->has('agenda.lembretes', 1)
                ->where('agenda.lembretes.0.titulo', 'Trocar bolsa')
                ->where('agenda.lembretes.0.horario', '08:00')
                ->where('agenda.ehHoje', true),
            );
    }

    public function test_paciente_cria_lembrete(): void
    {
        $this->actingAs($this->maria)
            ->post(route('paciente.lembretes.store'), [
                'titulo' => 'Tomar medicação',
                'tipo' => 'medicamento',
                'horario' => '21:00',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lembretes', [
            'usuario_id' => $this->maria->id,
            'titulo' => 'Tomar medicação',
            'tipo' => 'medicamento',
        ]);
    }

    public function test_lembrete_exige_horario_em_formato_valido(): void
    {
        $this->actingAs($this->maria)
            ->post(route('paciente.lembretes.store'), [
                'titulo' => 'Tomar medicação',
                'tipo' => 'medicamento',
                'horario' => 'de manhã',
            ])
            ->assertSessionHasErrors('horario');
    }

    public function test_paciente_liga_e_desliga_o_proprio_lembrete(): void
    {
        $lembrete = Lembrete::factory()->create(['usuario_id' => $this->maria->id, 'ativo' => true]);

        $this->actingAs($this->maria)
            ->patch(route('paciente.lembretes.alternar', $lembrete));

        $this->assertFalse($lembrete->refresh()->ativo);

        $this->actingAs($this->maria)
            ->patch(route('paciente.lembretes.alternar', $lembrete));

        $this->assertTrue($lembrete->refresh()->ativo);
    }

    public function test_paciente_nao_altera_lembrete_de_outro_paciente(): void
    {
        $lembreteDaMaria = Lembrete::factory()->create([
            'usuario_id' => $this->maria->id,
            'ativo' => true,
        ]);

        $this->actingAs($this->joao)
            ->patch(route('paciente.lembretes.alternar', $lembreteDaMaria))
            ->assertForbidden();

        $this->assertTrue($lembreteDaMaria->refresh()->ativo);
    }

    public function test_paciente_nao_apaga_lembrete_de_outro_paciente(): void
    {
        $lembreteDaMaria = Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->joao)
            ->delete(route('paciente.lembretes.destroy', $lembreteDaMaria))
            ->assertForbidden();

        $this->assertModelExists($lembreteDaMaria);
    }

    public function test_paciente_apaga_o_proprio_lembrete(): void
    {
        $lembrete = Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->maria)
            ->delete(route('paciente.lembretes.destroy', $lembrete));

        $this->assertModelMissing($lembrete);
    }

    public function test_marcar_lembrete_como_feito_gera_registro_no_diario(): void
    {
        $lembrete = Lembrete::factory()->create([
            'usuario_id' => $this->maria->id,
            'titulo' => 'Trocar bolsa',
            'tipo' => TipoDeLembrete::Troca,
        ]);

        $this->actingAs($this->maria)
            ->post(route('paciente.lembretes.concluir', $lembrete))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('registros_de_cuidados', [
            'usuario_id' => $this->maria->id,
            'lembrete_id' => $lembrete->id,
            'titulo' => 'Trocar bolsa',
            'tipo' => 'troca',
        ]);
    }

    public function test_lembrete_concluido_hoje_aparece_marcado_na_agenda(): void
    {
        $lembrete = Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->maria)->post(route('paciente.lembretes.concluir', $lembrete));

        $this->actingAs($this->maria)
            ->get(route('paciente.lembretes.index'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('agenda.lembretes.0.concluido', true),
            );
    }

    public function test_paciente_nao_conclui_lembrete_de_outro_paciente(): void
    {
        $lembreteDaMaria = Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->joao)
            ->post(route('paciente.lembretes.concluir', $lembreteDaMaria))
            ->assertForbidden();

        $this->assertDatabaseCount('registros_de_cuidados', 0);
    }

    public function test_dia_invalido_na_url_cai_em_hoje_sem_quebrar(): void
    {
        Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.lembretes.index', ['dia' => 'ontem-de-manha']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('agenda.ehHoje', true),
            );
    }

    public function test_dia_futuro_na_url_cai_em_hoje(): void
    {
        Lembrete::factory()->create(['usuario_id' => $this->maria->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.lembretes.index', ['dia' => now()->addYear()->toDateString()]))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->where('agenda.ehHoje', true),
            );
    }

    // ------------------------------------------------------------------
    // Diário
    // ------------------------------------------------------------------

    public function test_diario_traz_resumo_da_semana_e_historico_do_paciente(): void
    {
        RegistroDeCuidado::factory()->count(3)->create([
            'usuario_id' => $this->maria->id,
            'tipo' => TipoDeLembrete::Troca,
            'realizado_em' => now(),
        ]);
        RegistroDeCuidado::factory()->create(['usuario_id' => $this->joao->id]);

        $this->actingAs($this->maria)
            ->get(route('paciente.diario'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina
                ->component('Paciente/Diario')
                ->where('primeiroNome', 'Maria')
                ->has('historico', 3)
                ->where('resumoDaSemana.trocas', 3),
            );
    }

    public function test_paciente_registra_cuidado_avulso(): void
    {
        $this->actingAs($this->maria)
            ->post(route('paciente.diario.registrar'), [
                'titulo' => 'Higienização do local',
                'tipo' => 'higiene',
                'observacao' => 'Pele sem vermelhidão.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('registros_de_cuidados', [
            'usuario_id' => $this->maria->id,
            'titulo' => 'Higienização do local',
            'tipo' => 'higiene',
            'observacao' => 'Pele sem vermelhidão.',
            'lembrete_id' => null,
        ]);
    }

    public function test_registro_de_cuidado_exige_titulo_e_tipo(): void
    {
        $this->actingAs($this->maria)
            ->post(route('paciente.diario.registrar'), [])
            ->assertSessionHasErrors(['titulo', 'tipo']);
    }

    public function test_historico_de_um_paciente_nunca_aparece_para_o_outro(): void
    {
        RegistroDeCuidado::factory()->create([
            'usuario_id' => $this->maria->id,
            'titulo' => 'Cuidado da Maria',
        ]);

        $this->actingAs($this->joao)
            ->get(route('paciente.diario'))
            ->assertInertia(fn (AssertableInertia $pagina) => $pagina->has('historico', 0));
    }
}
