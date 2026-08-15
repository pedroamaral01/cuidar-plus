<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\SinalDeAlerta;
use App\Models\Usuario;
use App\Repositories\Contracts\SinalDeAlertaRepositoryInterface;
use Illuminate\Support\Collection;

class SinalDeAlertaRepository implements SinalDeAlertaRepositoryInterface
{
    public function buscarPublicadosParaDispositivo(?int $dispositivoId): Collection
    {
        return SinalDeAlerta::publicados()
            ->paraDispositivo($dispositivoId)
            // Alta primeiro: é o que o paciente precisa ver antes de tudo.
            ->orderByRaw("FIELD(gravidade, 'alta', 'media', 'baixa')")
            ->orderBy('nome')
            ->get();
    }

    public function buscarPacientesAfetados(?int $dispositivoId): Collection
    {
        $consulta = Usuario::query()->pacientes()->where('ativo', true);

        // Sinal geral alcança todos os pacientes; sinal de dispositivo alcança
        // apenas quem tem aquele dispositivo ativo.
        if ($dispositivoId !== null) {
            $consulta->whereHas('dispositivos', function ($filtro) use ($dispositivoId): void {
                $filtro->where('dispositivos.id', $dispositivoId)
                    ->where('usuarios_dispositivos.ativo', true);
            });
        }

        return $consulta->get();
    }
}
