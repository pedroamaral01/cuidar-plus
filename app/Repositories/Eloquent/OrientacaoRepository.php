<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Orientacao;
use App\Repositories\Contracts\OrientacaoRepositoryInterface;
use Illuminate\Support\Collection;

class OrientacaoRepository implements OrientacaoRepositoryInterface
{
    public function buscarPublicadasPorDispositivo(?int $dispositivoId): Collection
    {
        if ($dispositivoId === null) {
            return new Collection;
        }

        return Orientacao::publicadas()
            ->where('dispositivo_id', $dispositivoId)
            ->orderBy('titulo')
            ->get();
    }

    public function buscarPublicadaComPassos(int $orientacaoId, ?int $dispositivoId): ?Orientacao
    {
        if ($dispositivoId === null) {
            return null;
        }

        return Orientacao::publicadas()
            ->with(['tiposDeCuidado.passos'])
            ->where('dispositivo_id', $dispositivoId)
            ->find($orientacaoId);
    }
}
