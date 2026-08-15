<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RegistroDeCuidadoRepository implements RegistroDeCuidadoRepositoryInterface
{
    public function criar(Usuario $paciente, array $dados): RegistroDeCuidado
    {
        return RegistroDeCuidado::create([
            ...$dados,
            'usuario_id' => $paciente->id,
        ]);
    }

    public function buscarHistoricoPorPaciente(Usuario $paciente, int $limite = 50): Collection
    {
        return RegistroDeCuidado::doPaciente($paciente->id)
            ->maisRecentesPrimeiro()
            ->limit($limite)
            ->get();
    }

    public function buscarPorPacienteNoPeriodo(Usuario $paciente, Carbon $inicio, Carbon $fim): Collection
    {
        return RegistroDeCuidado::doPaciente($paciente->id)
            ->whereBetween('realizado_em', [$inicio, $fim])
            ->maisRecentesPrimeiro()
            ->get();
    }

    public function buscarIdsDeLembretesConcluidosNoDia(Usuario $paciente, Carbon $dia): array
    {
        return RegistroDeCuidado::doPaciente($paciente->id)
            ->whereNotNull('lembrete_id')
            ->whereDate('realizado_em', $dia->toDateString())
            ->pluck('lembrete_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function contarPorTipoNoPeriodo(Usuario $paciente, Carbon $inicio, Carbon $fim): array
    {
        return RegistroDeCuidado::doPaciente($paciente->id)
            ->whereBetween('realizado_em', [$inicio, $fim])
            ->selectRaw('tipo, COUNT(*) as quantidade')
            ->groupBy('tipo')
            ->pluck('quantidade', 'tipo')
            ->map(fn ($quantidade): int => (int) $quantidade)
            ->all();
    }
}
