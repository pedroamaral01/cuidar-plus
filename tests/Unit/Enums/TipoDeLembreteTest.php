<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\TipoDeLembrete;
use PHPUnit\Framework\TestCase;

class TipoDeLembreteTest extends TestCase
{
    public function test_todo_tipo_tem_rotulo_icone_e_cor(): void
    {
        foreach (TipoDeLembrete::cases() as $tipo) {
            $this->assertNotSame('', $tipo->rotulo(), "Tipo {$tipo->value} sem rótulo.");
            $this->assertNotSame('', $tipo->icone(), "Tipo {$tipo->value} sem ícone.");
            $this->assertContains(
                $tipo->cor(),
                ['teal', 'amber', 'coral', 'lavender'],
                "Tipo {$tipo->value} usa cor fora da paleta da marca.",
            );
        }
    }

    public function test_cobre_os_cuidados_previstos_no_plano(): void
    {
        $valores = array_map(fn (TipoDeLembrete $tipo): string => $tipo->value, TipoDeLembrete::cases());

        $this->assertSame(
            ['troca', 'esvaziamento', 'higiene', 'protecao', 'medicamento', 'outro'],
            $valores,
        );
    }
}
