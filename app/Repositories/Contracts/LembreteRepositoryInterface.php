<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Lembrete;
use App\Models\Usuario;
use Illuminate\Support\Collection;

interface LembreteRepositoryInterface
{
    /** Todos os lembretes do paciente, do mais cedo para o mais tarde. */
    public function buscarPorPaciente(Usuario $paciente): Collection;

    /** Apenas os lembretes ligados — é o que o paciente vê no dia. */
    public function buscarAtivosPorPaciente(Usuario $paciente): Collection;

    /**
     * Lembretes ativos que caem dentro da janela de horário informada e que
     * ainda não geraram notificação hoje. Usado pelo agendador.
     *
     * @return Collection<int, Lembrete>
     */
    public function buscarAtivosNaJanelaDeHorario(string $inicio, string $fim): Collection;

    public function criar(Usuario $paciente, array $dados): Lembrete;

    public function atualizar(Lembrete $lembrete, array $dados): Lembrete;

    public function remover(Lembrete $lembrete): void;

    public function marcarComoNotificado(Lembrete $lembrete): void;
}
