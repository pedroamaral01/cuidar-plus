<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Models\ConteudoEducativo;
use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\Usuario;
use App\Services\Paciente\ConteudoEducativoService;
use App\Services\Paciente\OrientacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ServicosDoPacienteTest extends TestCase
{
    use RefreshDatabase;

    private function pacienteComDispositivo(Dispositivo $dispositivo): Usuario
    {
        $paciente = Usuario::factory()->paciente()->create();
        $paciente->dispositivos()->attach($dispositivo, ['ativo' => true]);

        return $paciente;
    }

    public function test_paciente_so_ve_orientacoes_do_proprio_dispositivo(): void
    {
        $servico = app(OrientacaoService::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        Orientacao::factory()->count(2)->create(['dispositivo_id' => $colostomia->id]);
        Orientacao::factory()->create(['dispositivo_id' => $sonda->id]);

        $paciente = $this->pacienteComDispositivo($colostomia);

        $this->assertCount(2, $servico->listarParaPaciente($paciente));
    }

    public function test_abrir_orientacao_de_outro_dispositivo_resulta_em_nao_encontrado(): void
    {
        $servico = app(OrientacaoService::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        $orientacaoDaSonda = Orientacao::factory()->create(['dispositivo_id' => $sonda->id]);
        $paciente = $this->pacienteComDispositivo($colostomia);

        $this->expectException(NotFoundHttpException::class);

        $servico->detalharParaPaciente($paciente, $orientacaoDaSonda->id);
    }

    public function test_paciente_sem_dispositivo_nao_ve_orientacao_nenhuma(): void
    {
        $servico = app(OrientacaoService::class);
        Orientacao::factory()->count(3)->create();

        $paciente = Usuario::factory()->paciente()->create();

        $this->assertCount(0, $servico->listarParaPaciente($paciente));
    }

    public function test_favoritar_conteudo_indisponivel_resulta_em_nao_encontrado(): void
    {
        $servico = app(ConteudoEducativoService::class);
        $dispositivo = Dispositivo::factory()->create();
        $paciente = $this->pacienteComDispositivo($dispositivo);

        $despublicado = ConteudoEducativo::factory()->despublicado()->create();

        $this->expectException(NotFoundHttpException::class);

        $servico->alternarFavorito($paciente, $despublicado->id);
    }

    public function test_favoritar_conteudo_de_outro_dispositivo_resulta_em_nao_encontrado(): void
    {
        $servico = app(ConteudoEducativoService::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        $paciente = $this->pacienteComDispositivo($colostomia);
        $conteudoDaSonda = ConteudoEducativo::factory()->create(['dispositivo_id' => $sonda->id]);

        $this->expectException(NotFoundHttpException::class);

        $servico->alternarFavorito($paciente, $conteudoDaSonda->id);
    }

    public function test_favoritar_e_desfavoritar_conteudo_visivel(): void
    {
        $servico = app(ConteudoEducativoService::class);
        $dispositivo = Dispositivo::factory()->create();
        $paciente = $this->pacienteComDispositivo($dispositivo);

        $conteudo = ConteudoEducativo::factory()->create();

        $this->assertTrue($servico->alternarFavorito($paciente, $conteudo->id));
        $this->assertFalse($servico->alternarFavorito($paciente, $conteudo->id));
    }

    public function test_listagem_de_conteudos_mistura_gerais_e_do_dispositivo(): void
    {
        $servico = app(ConteudoEducativoService::class);
        $colostomia = Dispositivo::factory()->create();
        $sonda = Dispositivo::factory()->create();

        ConteudoEducativo::factory()->create(['titulo' => 'Geral']);
        ConteudoEducativo::factory()->create(['titulo' => 'Da colostomia', 'dispositivo_id' => $colostomia->id]);
        ConteudoEducativo::factory()->create(['titulo' => 'Da sonda', 'dispositivo_id' => $sonda->id]);

        $paciente = $this->pacienteComDispositivo($colostomia);
        $titulos = $servico->listarParaPaciente($paciente)->pluck('titulo')->all();

        $this->assertSame(['Da colostomia', 'Geral'], $titulos);
    }
}
