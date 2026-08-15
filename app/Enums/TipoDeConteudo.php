<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Formato de um conteúdo educativo. Alimenta o filtro
 * "Todos / Vídeos / Favoritos" da tela de conteúdos.
 */
enum TipoDeConteudo: string
{
    case Texto = 'texto';
    case Video = 'video';

    public function rotulo(): string
    {
        return match ($this) {
            self::Texto => 'Texto',
            self::Video => 'Vídeo',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::Texto => 'FileText',
            self::Video => 'PlayCircle',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Texto => 'teal',
            self::Video => 'lavender',
        };
    }
}
