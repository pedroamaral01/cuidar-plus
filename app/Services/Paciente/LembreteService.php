<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Lembrete;
use App\Models\Usuario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use Illuminate\Support\Collection;

class LembreteService
{
    public function __construct(
        private readonly LembreteRepositoryInterface $lembretes,
    ) {}

    public function buscarLembretesDoPaciente(Usuario $paciente): Collection
    {
        return $this->lembretes->buscarPorPaciente($paciente);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function criarLembrete(Usuario $paciente, array $dados): Lembrete
    {
        return $this->lembretes->criar($paciente, $dados);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function atualizarLembrete(Lembrete $lembrete, array $dados): Lembrete
    {
        return $this->lembretes->atualizar($lembrete, $dados);
    }

    /** Liga/desliga o lembrete e devolve o estado final. */
    public function alternarAtivacao(Lembrete $lembrete): bool
    {
        $ativo = ! $lembrete->ativo;

        $this->lembretes->atualizar($lembrete, ['ativo' => $ativo]);

        return $ativo;
    }

    public function removerLembrete(Lembrete $lembrete): void
    {
        $this->lembretes->remover($lembrete);
    }
}
