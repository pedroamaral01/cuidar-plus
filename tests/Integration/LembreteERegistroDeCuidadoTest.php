<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\TipoDeLembrete;
use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LembreteERegistroDeCuidadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_lembrete_pertence_ao_paciente_que_o_criou(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        Lembrete::factory()->count(3)->create(['usuario_id' => $maria->id]);
        Lembrete::factory()->create(['usuario_id' => $joao->id]);

        $this->assertCount(3, Lembrete::doPaciente($maria->id)->get());
        $this->assertCount(1, Lembrete::doPaciente($joao->id)->get());
    }

    public function test_escopo_de_ativos_ignora_lembrete_desligado(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        Lembrete::factory()->count(2)->create(['usuario_id' => $paciente->id]);
        Lembrete::factory()->inativo()->create(['usuario_id' => $paciente->id]);

        $this->assertCount(2, Lembrete::doPaciente($paciente->id)->ativos()->get());
        $this->assertCount(3, Lembrete::doPaciente($paciente->id)->get());
    }

    public function test_horario_formatado_para_a_interface(): void
    {
        $lembrete = Lembrete::factory()->noHorario('08:00:00')->create();

        $this->assertSame('08:00', $lembrete->horarioFormatado());
    }

    public function test_registro_de_cuidado_pode_nascer_de_um_lembrete(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $lembrete = Lembrete::factory()->create([
            'usuario_id' => $paciente->id,
            'tipo' => TipoDeLembrete::Troca,
        ]);

        $registro = RegistroDeCuidado::create([
            'usuario_id' => $paciente->id,
            'lembrete_id' => $lembrete->id,
            'titulo' => 'Troca de bolsa realizada',
            'tipo' => TipoDeLembrete::Troca,
            'realizado_em' => now(),
        ]);

        $this->assertTrue($registro->lembrete->is($lembrete));
        $this->assertCount(1, $lembrete->registrosDeCuidados()->get());
    }

    public function test_apagar_lembrete_preserva_o_historico_do_paciente(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        $lembrete = Lembrete::factory()->create(['usuario_id' => $paciente->id]);
        $registro = RegistroDeCuidado::factory()->create([
            'usuario_id' => $paciente->id,
            'lembrete_id' => $lembrete->id,
        ]);

        $lembrete->delete();

        $this->assertModelExists($registro);
        $this->assertNull($registro->fresh()->lembrete_id);
    }

    public function test_historico_vem_do_mais_recente_para_o_mais_antigo(): void
    {
        $paciente = Usuario::factory()->paciente()->create();

        RegistroDeCuidado::factory()->realizadoEm('2026-08-10 08:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Mais antigo']);
        RegistroDeCuidado::factory()->realizadoEm('2026-08-14 08:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Mais recente']);
        RegistroDeCuidado::factory()->realizadoEm('2026-08-12 08:00:00')->create(['usuario_id' => $paciente->id, 'titulo' => 'Do meio']);

        $titulos = RegistroDeCuidado::doPaciente($paciente->id)->maisRecentesPrimeiro()->pluck('titulo')->all();

        $this->assertSame(['Mais recente', 'Do meio', 'Mais antigo'], $titulos);
    }

    public function test_historico_de_um_paciente_nao_alcanca_o_outro(): void
    {
        $maria = Usuario::factory()->paciente()->create();
        $joao = Usuario::factory()->paciente()->create();

        RegistroDeCuidado::factory()->count(2)->create(['usuario_id' => $maria->id]);
        RegistroDeCuidado::factory()->create(['usuario_id' => $joao->id]);

        $this->assertCount(2, RegistroDeCuidado::doPaciente($maria->id)->get());
        $this->assertCount(1, RegistroDeCuidado::doPaciente($joao->id)->get());
    }

    public function test_apagar_paciente_leva_junto_lembretes_e_registros(): void
    {
        $paciente = Usuario::factory()->paciente()->create();
        Lembrete::factory()->create(['usuario_id' => $paciente->id]);
        RegistroDeCuidado::factory()->create(['usuario_id' => $paciente->id]);

        $paciente->delete();

        $this->assertDatabaseCount('lembretes', 0);
        $this->assertDatabaseCount('registros_de_cuidados', 0);
    }
}
