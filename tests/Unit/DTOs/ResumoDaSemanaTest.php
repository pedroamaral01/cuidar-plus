<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\ResumoDaSemana;
use PHPUnit\Framework\TestCase;

class ResumoDaSemanaTest extends TestCase
{
    public function test_calcula_percentual_a_partir_dos_cuidados_realizados(): void
    {
        $resumo = ResumoDaSemana::calcular(['troca' => 5, 'higiene' => 3], previstos: 10);

        $this->assertSame(8, $resumo->realizados);
        $this->assertSame(10, $resumo->previstos);
        $this->assertSame(80, $resumo->percentual);
    }

    public function test_paciente_sem_lembretes_ativos_nao_divide_por_zero(): void
    {
        $resumo = ResumoDaSemana::calcular(['troca' => 2], previstos: 0);

        $this->assertSame(2, $resumo->realizados);
        $this->assertSame(0, $resumo->percentual);
    }

    public function test_percentual_nunca_passa_de_cem(): void
    {
        $resumo = ResumoDaSemana::calcular(['troca' => 20], previstos: 5);

        $this->assertSame(100, $resumo->percentual);
    }

    public function test_separa_trocas_e_esvaziamentos_para_os_indicadores_do_diario(): void
    {
        $resumo = ResumoDaSemana::calcular(
            ['troca' => 3, 'esvaziamento' => 7, 'higiene' => 2],
            previstos: 20,
        );

        $this->assertSame(3, $resumo->trocas);
        $this->assertSame(7, $resumo->esvaziamentos);
        $this->assertSame(12, $resumo->registros);
    }

    public function test_tipo_ausente_conta_como_zero(): void
    {
        $resumo = ResumoDaSemana::calcular(['higiene' => 4], previstos: 8);

        $this->assertSame(0, $resumo->trocas);
        $this->assertSame(0, $resumo->esvaziamentos);
    }

    public function test_converte_para_array_pronto_para_o_inertia(): void
    {
        $resumo = ResumoDaSemana::calcular(['troca' => 1], previstos: 2);

        $this->assertSame(
            ['realizados', 'previstos', 'percentual', 'trocas', 'esvaziamentos', 'registros'],
            array_keys($resumo->paraArray()),
        );
    }
}
