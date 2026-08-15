<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Lembrete;
use App\Models\Usuario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use Illuminate\Support\Collection;

class LembreteRepository implements LembreteRepositoryInterface
{
    public function buscarPorPaciente(Usuario $paciente): Collection
    {
        return Lembrete::doPaciente($paciente->id)
            ->orderBy('horario')
            ->get();
    }

    public function buscarAtivosPorPaciente(Usuario $paciente): Collection
    {
        return Lembrete::doPaciente($paciente->id)
            ->ativos()
            ->orderBy('horario')
            ->get();
    }

    public function buscarAtivosNaJanelaDeHorario(string $inicio, string $fim): Collection
    {
        return Lembrete::query()
            ->with('usuario')
            ->ativos()
            ->whereBetween('horario', [$inicio, $fim])
            // Um lembrete só volta a notificar no dia seguinte.
            ->where(function ($consulta): void {
                $consulta->whereNull('notificado_em')
                    ->orWhereDate('notificado_em', '<', now()->toDateString());
            })
            ->orderBy('horario')
            ->get();
    }

    public function criar(Usuario $paciente, array $dados): Lembrete
    {
        return Lembrete::create([
            ...$dados,
            'usuario_id' => $paciente->id,
        ]);
    }

    public function atualizar(Lembrete $lembrete, array $dados): Lembrete
    {
        $lembrete->update($dados);

        return $lembrete->refresh();
    }

    public function remover(Lembrete $lembrete): void
    {
        $lembrete->delete();
    }

    public function marcarComoNotificado(Lembrete $lembrete): void
    {
        $lembrete->forceFill(['notificado_em' => now()])->save();
    }
}
