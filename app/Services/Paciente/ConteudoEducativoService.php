<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\ConteudoEducativo;
use App\Models\Usuario;
use App\Repositories\Contracts\ConteudoEducativoRepositoryInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConteudoEducativoService
{
    public function __construct(
        private readonly ConteudoEducativoRepositoryInterface $conteudos,
    ) {}

    public function listarParaPaciente(Usuario $paciente): Collection
    {
        return $this->conteudos->buscarPublicadosParaPaciente(
            $paciente,
            $paciente->dispositivoAtual()?->id,
        );
    }

    /**
     * Liga/desliga o favorito. O conteúdo precisa estar publicado e visível
     * para o paciente — favoritar por id não pode ser um atalho para acessar
     * conteúdo de outro dispositivo ou despublicado.
     */
    public function alternarFavorito(Usuario $paciente, int $conteudoId): bool
    {
        $conteudo = $this->conteudos->buscarPublicadoVisivelParaPaciente(
            $conteudoId,
            $paciente->dispositivoAtual()?->id,
        );

        if (! $conteudo instanceof ConteudoEducativo) {
            throw new NotFoundHttpException('Conteúdo educativo indisponível.');
        }

        return $this->conteudos->alternarFavorito($paciente, $conteudo);
    }
}
