<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Resumo semanal de cuidados — alimenta o anel de progresso do Início e os
 * indicadores do Diário.
 */
final readonly class ResumoDaSemana
{
    public function __construct(
        public int $realizados,
        public int $previstos,
        public int $percentual,
        public int $trocas,
        public int $esvaziamentos,
        public int $registros,
    ) {}

    /**
     * @param  array<string, int>  $porTipo  tipo => quantidade
     */
    public static function calcular(array $porTipo, int $previstos): self
    {
        $realizados = array_sum($porTipo);

        // Sem lembretes ativos não há meta a atingir: o anel fica zerado em
        // vez de dividir por zero.
        $percentual = $previstos > 0
            ? (int) min(100, round($realizados / $previstos * 100))
            : 0;

        return new self(
            realizados: $realizados,
            previstos: $previstos,
            percentual: $percentual,
            trocas: $porTipo['troca'] ?? 0,
            esvaziamentos: $porTipo['esvaziamento'] ?? 0,
            registros: $realizados,
        );
    }

    /** @return array<string, int> */
    public function paraArray(): array
    {
        return [
            'realizados' => $this->realizados,
            'previstos' => $this->previstos,
            'percentual' => $this->percentual,
            'trocas' => $this->trocas,
            'esvaziamentos' => $this->esvaziamentos,
            'registros' => $this->registros,
        ];
    }
}
