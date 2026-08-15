<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Gravidade de um sinal de alerta. Define o destaque visual na lista do
 * paciente e a ordem de exibição (mais grave primeiro).
 */
enum GravidadeDoAlerta: string
{
    case Alta = 'alta';
    case Media = 'media';
    case Baixa = 'baixa';

    public function rotulo(): string
    {
        return match ($this) {
            self::Alta => 'Alta',
            self::Media => 'Média',
            self::Baixa => 'Baixa',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Alta => 'coral',
            self::Media => 'amber',
            self::Baixa => 'teal',
        };
    }

    /** Menor valor aparece primeiro na listagem. */
    public function peso(): int
    {
        return match ($this) {
            self::Alta => 1,
            self::Media => 2,
            self::Baixa => 3,
        };
    }
}
