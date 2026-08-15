<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Orientacao;
use App\Models\Usuario;
use App\Repositories\Contracts\OrientacaoRepositoryInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrientacaoService
{
    public function __construct(
        private readonly OrientacaoRepositoryInterface $orientacoes,
    ) {}

    public function listarParaPaciente(Usuario $paciente): Collection
    {
        return $this->orientacoes->buscarPublicadasPorDispositivo(
            $paciente->dispositivoAtual()?->id,
        );
    }

    /**
     * Detalhe com abas e passo a passo. A orientação precisa pertencer ao
     * dispositivo do paciente — sem isso, trocar o id na URL daria acesso a
     * orientação de outro dispositivo.
     */
    public function detalharParaPaciente(Usuario $paciente, int $orientacaoId): Orientacao
    {
        $orientacao = $this->orientacoes->buscarPublicadaComPassos(
            $orientacaoId,
            $paciente->dispositivoAtual()?->id,
        );

        if (! $orientacao instanceof Orientacao) {
            throw new NotFoundHttpException('Orientação indisponível para o seu dispositivo.');
        }

        return $orientacao;
    }
}
