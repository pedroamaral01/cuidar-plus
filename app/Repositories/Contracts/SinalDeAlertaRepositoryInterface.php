<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Usuario;
use Illuminate\Support\Collection;

interface SinalDeAlertaRepositoryInterface
{
    /**
     * Sinais publicados visíveis para o dispositivo (os específicos dele mais
     * os gerais), ordenados da maior para a menor gravidade.
     */
    public function buscarPublicadosParaDispositivo(?int $dispositivoId): Collection;

    /** Pacientes que devem ser avisados quando um sinal é publicado. */
    public function buscarPacientesAfetados(?int $dispositivoId): Collection;
}
