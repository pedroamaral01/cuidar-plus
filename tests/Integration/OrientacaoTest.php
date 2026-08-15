<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\PassoDeOrientacao;
use App\Models\TipoDeCuidado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrientacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_orientacao_organiza_abas_e_passos_na_ordem_cadastrada(): void
    {
        $orientacao = Orientacao::factory()->create();

        $troca = TipoDeCuidado::create([
            'orientacao_id' => $orientacao->id,
            'nome' => 'Troca da bolsa',
            'ordem' => 0,
        ]);
        TipoDeCuidado::create([
            'orientacao_id' => $orientacao->id,
            'nome' => 'Higiene',
            'ordem' => 1,
        ]);

        // Inseridos fora de ordem de propósito.
        PassoDeOrientacao::create(['tipo_de_cuidado_id' => $troca->id, 'descricao' => 'Fixe a nova bolsa.', 'ordem' => 2]);
        PassoDeOrientacao::create(['tipo_de_cuidado_id' => $troca->id, 'descricao' => 'Reúna o material.', 'ordem' => 0]);
        PassoDeOrientacao::create(['tipo_de_cuidado_id' => $troca->id, 'descricao' => 'Limpe a pele.', 'ordem' => 1]);

        $abas = $orientacao->tiposDeCuidado()->with('passos')->get();

        $this->assertSame(['Troca da bolsa', 'Higiene'], $abas->pluck('nome')->all());
        $this->assertSame(
            ['Reúna o material.', 'Limpe a pele.', 'Fixe a nova bolsa.'],
            $abas->first()->passos->pluck('descricao')->all(),
        );
    }

    public function test_orientacao_pertence_ao_dispositivo_e_so_aparece_nele(): void
    {
        $colostomia = Dispositivo::factory()->create(['nome' => 'Colostomia']);
        $sonda = Dispositivo::factory()->create(['nome' => 'Sonda vesical']);

        Orientacao::factory()->count(2)->create(['dispositivo_id' => $colostomia->id]);
        Orientacao::factory()->create(['dispositivo_id' => $sonda->id]);

        $this->assertCount(2, $colostomia->orientacoes()->get());
        $this->assertCount(1, $sonda->orientacoes()->get());
    }

    public function test_orientacao_despublicada_fica_fora_da_listagem_do_paciente(): void
    {
        Orientacao::factory()->count(2)->create();
        Orientacao::factory()->despublicada()->create();

        $this->assertCount(2, Orientacao::publicadas()->get());
    }

    public function test_apagar_orientacao_remove_abas_e_passos_em_cascata(): void
    {
        $orientacao = Orientacao::factory()->create();
        $aba = TipoDeCuidado::create(['orientacao_id' => $orientacao->id, 'nome' => 'Higiene', 'ordem' => 0]);
        PassoDeOrientacao::create(['tipo_de_cuidado_id' => $aba->id, 'descricao' => 'Lave as mãos.', 'ordem' => 0]);

        $orientacao->delete();

        $this->assertDatabaseCount('tipos_de_cuidado', 0);
        $this->assertDatabaseCount('passos_de_orientacao', 0);
    }
}
