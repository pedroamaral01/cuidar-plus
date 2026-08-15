<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Usuario;

/**
 * Para onde cada perfil vai depois de entrar no sistema. Fica isolado aqui
 * porque a decisão aparece em vários pontos (login, cadastro, verificação de
 * e-mail, middleware de perfil).
 */
final class DestinoPorPerfil
{
    public static function rotaInicial(?Usuario $usuario): string
    {
        if ($usuario === null) {
            return route('login');
        }

        return $usuario->ehAdministrador()
            ? route('administracao.painel')
            : route('paciente.inicio');
    }
}
