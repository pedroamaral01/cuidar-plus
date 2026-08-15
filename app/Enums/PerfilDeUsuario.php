<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Perfis de acesso do sistema. A separação entre a área do paciente e a área
 * administrativa é feita a partir deste valor.
 */
enum PerfilDeUsuario: string
{
    case Paciente = 'paciente';
    case Administrador = 'administrador';

    public function rotulo(): string
    {
        return match ($this) {
            self::Paciente => 'Paciente',
            self::Administrador => 'Administrador',
        };
    }

    public function ehAdministrador(): bool
    {
        return $this === self::Administrador;
    }

    public function ehPaciente(): bool
    {
        return $this === self::Paciente;
    }
}
