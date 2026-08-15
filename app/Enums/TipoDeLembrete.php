<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Tipos de cuidado que viram lembrete na rotina do paciente.
 *
 * O ícone e a cor acompanham o enum (e não o componente React) para que a
 * identidade visual de cada tipo venha do backend, junto com o dado.
 */
enum TipoDeLembrete: string
{
    case Troca = 'troca';
    case Esvaziamento = 'esvaziamento';
    case Higiene = 'higiene';
    case Protecao = 'protecao';
    case Medicamento = 'medicamento';
    case Outro = 'outro';

    public function rotulo(): string
    {
        return match ($this) {
            self::Troca => 'Troca',
            self::Esvaziamento => 'Esvaziamento',
            self::Higiene => 'Higiene',
            self::Protecao => 'Proteção da pele',
            self::Medicamento => 'Medicamento',
            self::Outro => 'Outro',
        };
    }

    /** Nome do ícone da biblioteca lucide-react usado no frontend. */
    public function icone(): string
    {
        return match ($this) {
            self::Troca => 'RefreshCw',
            self::Esvaziamento => 'Waves',
            self::Higiene => 'Droplets',
            self::Protecao => 'ShieldCheck',
            self::Medicamento => 'Pill',
            self::Outro => 'Circle',
        };
    }

    /** Chave da paleta de marca (teal, amber, coral, lavender). */
    public function cor(): string
    {
        return match ($this) {
            self::Troca => 'teal',
            self::Esvaziamento => 'amber',
            self::Higiene => 'lavender',
            self::Protecao => 'coral',
            self::Medicamento => 'teal',
            self::Outro => 'lavender',
        };
    }
}
