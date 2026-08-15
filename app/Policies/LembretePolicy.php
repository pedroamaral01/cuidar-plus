<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lembrete;
use App\Models\Usuario;

/**
 * Regra crítica de isolamento: um lembrete só pode ser visto, alterado ou
 * apagado pelo paciente dono dele.
 */
class LembretePolicy
{
    public function view(Usuario $usuario, Lembrete $lembrete): bool
    {
        return $this->ehDono($usuario, $lembrete);
    }

    public function update(Usuario $usuario, Lembrete $lembrete): bool
    {
        return $this->ehDono($usuario, $lembrete);
    }

    public function delete(Usuario $usuario, Lembrete $lembrete): bool
    {
        return $this->ehDono($usuario, $lembrete);
    }

    private function ehDono(Usuario $usuario, Lembrete $lembrete): bool
    {
        return $usuario->id === $lembrete->usuario_id;
    }
}
