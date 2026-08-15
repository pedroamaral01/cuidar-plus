<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\GravidadeDoAlerta;
use App\Models\Dispositivo;
use App\Models\SinalDeAlerta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SinalDeAlertaTest extends TestCase
{
    use RefreshDatabase;

    public function test_paciente_ve_os_sinais_gerais_e_os_do_seu_dispositivo(): void
    {
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        SinalDeAlerta::factory()->geral()->create(['nome' => 'Febre']);
        SinalDeAlerta::factory()->create(['dispositivo_id' => $colostomia->id, 'nome' => 'Vazamento na placa']);
        SinalDeAlerta::factory()->create(['dispositivo_id' => $sonda->id, 'nome' => 'Urina turva']);

        $visiveis = SinalDeAlerta::publicados()->paraDispositivo($colostomia->id)->pluck('nome')->all();

        sort($visiveis);
        $this->assertSame(['Febre', 'Vazamento na placa'], $visiveis);
    }

    public function test_paciente_sem_dispositivo_ve_apenas_os_sinais_gerais(): void
    {
        $dispositivo = Dispositivo::factory()->create();

        SinalDeAlerta::factory()->geral()->create(['nome' => 'Febre']);
        SinalDeAlerta::factory()->create(['dispositivo_id' => $dispositivo->id, 'nome' => 'Vazamento']);

        $visiveis = SinalDeAlerta::publicados()->paraDispositivo(null)->pluck('nome')->all();

        $this->assertSame(['Febre'], $visiveis);
    }

    public function test_sinal_despublicado_nao_chega_ao_paciente(): void
    {
        SinalDeAlerta::factory()->geral()->create();
        SinalDeAlerta::factory()->geral()->create(['publicado' => false]);

        $this->assertCount(1, SinalDeAlerta::publicados()->get());
        $this->assertCount(2, SinalDeAlerta::all());
    }

    public function test_gravidade_e_persistida_como_enum(): void
    {
        $sinal = SinalDeAlerta::factory()->geral()->comGravidade(GravidadeDoAlerta::Alta)->create();

        $this->assertDatabaseHas('sinais_de_alerta', ['id' => $sinal->id, 'gravidade' => 'alta']);
        $this->assertSame(GravidadeDoAlerta::Alta, $sinal->fresh()->gravidade);
    }

    public function test_apagar_dispositivo_transforma_o_sinal_em_geral(): void
    {
        $dispositivo = Dispositivo::factory()->create();
        $sinal = SinalDeAlerta::factory()->create(['dispositivo_id' => $dispositivo->id]);

        $dispositivo->delete();

        $this->assertNull($sinal->fresh()->dispositivo_id);
    }
}
