<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Usuario;
use App\Repositories\Contracts\SinalDeAlertaRepositoryInterface;
use Illuminate\Support\Collection;

class SinalDeAlertaService
{
    public function __construct(
        private readonly SinalDeAlertaRepositoryInterface $sinais,
    ) {}

    public function listarParaPaciente(Usuario $paciente): Collection
    {
        return $this->sinais->buscarPublicadosParaDispositivo(
            $paciente->dispositivoAtual()?->id,
        );
    }
}
