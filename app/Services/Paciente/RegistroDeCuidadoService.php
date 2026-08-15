<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\DTOs\ResumoDaSemana;
use App\Enums\TipoDeLembrete;
use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Regras do diário de cuidados: registrar o que foi feito e resumir a semana.
 */
class RegistroDeCuidadoService
{
    public function __construct(
        private readonly RegistroDeCuidadoRepositoryInterface $registros,
        private readonly LembreteRepositoryInterface $lembretes,
    ) {}

    /** Registro avulso, feito pelo próprio paciente no diário. */
    public function registrarCuidado(
        Usuario $paciente,
        string $titulo,
        TipoDeLembrete $tipo,
        ?string $observacao = null,
    ): RegistroDeCuidado {
        return $this->registros->criar($paciente, [
            'titulo' => $titulo,
            'tipo' => $tipo,
            'observacao' => $observacao,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registro nascido de um lembrete ("marquei como feito"). O título e o
     * tipo vêm do lembrete, para o histórico ficar coerente com a rotina.
     */
    public function registrarCuidadoDoLembrete(Lembrete $lembrete, ?string $observacao = null): RegistroDeCuidado
    {
        return $this->registros->criar($lembrete->usuario, [
            'lembrete_id' => $lembrete->id,
            'titulo' => $lembrete->titulo,
            'tipo' => $lembrete->tipo,
            'observacao' => $observacao,
            'realizado_em' => now(),
        ]);
    }

    public function buscarHistoricoPorPaciente(Usuario $paciente, int $limite = 50): Collection
    {
        return $this->registros->buscarHistoricoPorPaciente($paciente, $limite);
    }

    /**
     * Resumo da semana corrente. O total previsto é a quantidade de lembretes
     * ativos multiplicada pelos dias já decorridos na semana — é a meta que o
     * anel de progresso compara.
     */
    public function montarResumoDaSemana(Usuario $paciente): ResumoDaSemana
    {
        $inicio = now()->startOfWeek();
        $fim = now()->endOfWeek();

        $porTipo = $this->registros->contarPorTipoNoPeriodo($paciente, $inicio, $fim);
        $lembretesAtivos = $this->lembretes->buscarAtivosPorPaciente($paciente)->count();
        $diasDecorridos = (int) $inicio->diffInDays(now()) + 1;

        return ResumoDaSemana::calcular($porTipo, $lembretesAtivos * $diasDecorridos);
    }
}
