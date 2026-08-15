<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Orientacao;
use Illuminate\Support\Collection;

interface OrientacaoRepositoryInterface
{
    /** Orientações publicadas do dispositivo, sem carregar abas e passos. */
    public function buscarPublicadasPorDispositivo(?int $dispositivoId): Collection;

    /**
     * Orientação publicada com abas e passo a passo já carregados.
     * Devolve null se a orientação não existir, não estiver publicada ou não
     * pertencer ao dispositivo informado.
     */
    public function buscarPublicadaComPassos(int $orientacaoId, ?int $dispositivoId): ?Orientacao;
}
