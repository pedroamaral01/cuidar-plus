<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\GravidadeDoAlerta;
use PHPUnit\Framework\TestCase;

class GravidadeDoAlertaTest extends TestCase
{
    public function test_ordena_da_mais_grave_para_a_menos_grave(): void
    {
        $gravidades = GravidadeDoAlerta::cases();

        usort($gravidades, fn (GravidadeDoAlerta $a, GravidadeDoAlerta $b): int => $a->peso() <=> $b->peso());

        $this->assertSame(
            [GravidadeDoAlerta::Alta, GravidadeDoAlerta::Media, GravidadeDoAlerta::Baixa],
            $gravidades,
        );
    }

    public function test_alta_usa_a_cor_de_atencao_da_marca(): void
    {
        $this->assertSame('coral', GravidadeDoAlerta::Alta->cor());
        $this->assertSame('amber', GravidadeDoAlerta::Media->cor());
        $this->assertSame('teal', GravidadeDoAlerta::Baixa->cor());
    }

    public function test_rotulo_media_usa_acentuacao_correta(): void
    {
        $this->assertSame('Média', GravidadeDoAlerta::Media->rotulo());
    }
}
