<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface RegistroDeCuidadoRepositoryInterface
{
    public function criar(Usuario $paciente, array $dados): RegistroDeCuidado;

    /** Histórico do paciente, do mais recente para o mais antigo. */
    public function buscarHistoricoPorPaciente(Usuario $paciente, int $limite = 50): Collection;

    /** @return Collection<int, RegistroDeCuidado> */
    public function buscarPorPacienteNoPeriodo(Usuario $paciente, Carbon $inicio, Carbon $fim): Collection;

    /**
     * Quantos cuidados de cada tipo o paciente registrou no período.
     *
     * @return array<string, int> tipo => quantidade
     */
    public function contarPorTipoNoPeriodo(Usuario $paciente, Carbon $inicio, Carbon $fim): array;
}
