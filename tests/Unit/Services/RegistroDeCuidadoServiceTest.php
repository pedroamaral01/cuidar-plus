<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\ResumoDaSemana;
use App\Enums\TipoDeLembrete;
use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use App\Services\Paciente\RegistroDeCuidadoService;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * O Service é testado sem banco: os repositories entram como dublês, o que só
 * é possível porque ele depende das interfaces, não do Eloquent.
 */
class RegistroDeCuidadoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_registrar_cuidado_avulso_marca_o_horario_atual(): void
    {
        $paciente = new Usuario;
        $paciente->id = 1;

        $registros = Mockery::mock(RegistroDeCuidadoRepositoryInterface::class);
        $registros->shouldReceive('criar')
            ->once()
            ->withArgs(function (Usuario $recebido, array $dados) use ($paciente): bool {
                return $recebido->is($paciente)
                    && $dados['titulo'] === 'Troca de bolsa'
                    && $dados['tipo'] === TipoDeLembrete::Troca
                    && $dados['observacao'] === 'Sem intercorrências'
                    && isset($dados['realizado_em']);
            })
            ->andReturn(new RegistroDeCuidado);

        $servico = new RegistroDeCuidadoService($registros, Mockery::mock(LembreteRepositoryInterface::class));

        $registro = $servico->registrarCuidado($paciente, 'Troca de bolsa', TipoDeLembrete::Troca, 'Sem intercorrências');

        $this->assertInstanceOf(RegistroDeCuidado::class, $registro);
    }

    public function test_registro_vindo_do_lembrete_herda_titulo_e_tipo(): void
    {
        $paciente = new Usuario;
        $paciente->id = 1;

        $lembrete = Mockery::mock(Lembrete::class)->makePartial();
        $lembrete->id = 7;
        $lembrete->titulo = 'Esvaziar bolsa';
        $lembrete->tipo = TipoDeLembrete::Esvaziamento;
        $lembrete->shouldReceive('getAttribute')->with('usuario')->andReturn($paciente);

        $registros = Mockery::mock(RegistroDeCuidadoRepositoryInterface::class);
        $registros->shouldReceive('criar')
            ->once()
            ->withArgs(function (Usuario $recebido, array $dados): bool {
                return $dados['lembrete_id'] === 7
                    && $dados['titulo'] === 'Esvaziar bolsa'
                    && $dados['tipo'] === TipoDeLembrete::Esvaziamento;
            })
            ->andReturn(new RegistroDeCuidado);

        $servico = new RegistroDeCuidadoService($registros, Mockery::mock(LembreteRepositoryInterface::class));

        $registro = $servico->registrarCuidadoDoLembrete($lembrete);

        $this->assertInstanceOf(RegistroDeCuidado::class, $registro);
    }

    public function test_resumo_da_semana_usa_a_meta_de_lembretes_ativos(): void
    {
        $paciente = new Usuario;
        $paciente->id = 1;

        $registros = Mockery::mock(RegistroDeCuidadoRepositoryInterface::class);
        $registros->shouldReceive('contarPorTipoNoPeriodo')
            ->once()
            ->andReturn(['troca' => 3, 'higiene' => 1]);

        $lembretes = Mockery::mock(LembreteRepositoryInterface::class);
        $lembretes->shouldReceive('buscarAtivosPorPaciente')
            ->once()
            ->andReturn(new Collection([new Lembrete, new Lembrete]));

        $servico = new RegistroDeCuidadoService($registros, $lembretes);
        $resumo = $servico->montarResumoDaSemana($paciente);

        $this->assertInstanceOf(ResumoDaSemana::class, $resumo);
        $this->assertSame(4, $resumo->realizados);
        $this->assertSame(3, $resumo->trocas);
        $this->assertGreaterThan(0, $resumo->previstos);
    }

    public function test_paciente_sem_lembretes_ativos_tem_resumo_zerado_sem_erro(): void
    {
        $paciente = new Usuario;
        $paciente->id = 1;

        $registros = Mockery::mock(RegistroDeCuidadoRepositoryInterface::class);
        $registros->shouldReceive('contarPorTipoNoPeriodo')->once()->andReturn([]);

        $lembretes = Mockery::mock(LembreteRepositoryInterface::class);
        $lembretes->shouldReceive('buscarAtivosPorPaciente')->once()->andReturn(new Collection);

        $servico = new RegistroDeCuidadoService($registros, $lembretes);
        $resumo = $servico->montarResumoDaSemana($paciente);

        $this->assertSame(0, $resumo->realizados);
        $this->assertSame(0, $resumo->percentual);
    }
}
