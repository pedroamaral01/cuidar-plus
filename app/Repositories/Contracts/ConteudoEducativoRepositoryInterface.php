<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ConteudoEducativo;
use App\Models\Usuario;
use Illuminate\Support\Collection;

interface ConteudoEducativoRepositoryInterface
{
    /**
     * Conteúdos publicados visíveis para o paciente, cada um já sabendo se
     * está entre os favoritos dele.
     */
    public function buscarPublicadosParaPaciente(Usuario $paciente, ?int $dispositivoId): Collection;

    /** Conteúdo publicado e visível para o paciente, ou null. */
    public function buscarPublicadoVisivelParaPaciente(int $conteudoId, ?int $dispositivoId): ?ConteudoEducativo;

    /** Liga/desliga o favorito e devolve o estado final. */
    public function alternarFavorito(Usuario $paciente, ConteudoEducativo $conteudo): bool;
}
