<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RegistroDeCuidado;
use App\Models\Usuario;

class RegistroDeCuidadoPolicy
{
    public function view(Usuario $usuario, RegistroDeCuidado $registro): bool
    {
        return $this->ehDono($usuario, $registro);
    }

    public function delete(Usuario $usuario, RegistroDeCuidado $registro): bool
    {
        return $this->ehDono($usuario, $registro);
    }

    private function ehDono(Usuario $usuario, RegistroDeCuidado $registro): bool
    {
        return $usuario->id === $registro->usuario_id;
    }
}
