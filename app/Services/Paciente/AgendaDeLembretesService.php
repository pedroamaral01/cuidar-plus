<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Usuario;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use Illuminate\Support\Carbon;

/**
 * Monta a agenda semanal de lembretes: a faixa de dias e, para o dia
 * escolhido, quais lembretes já foram concluídos.
 */
class AgendaDeLembretesService
{
    public function __construct(
        private readonly LembreteRepositoryInterface $lembretes,
        private readonly RegistroDeCuidadoRepositoryInterface $registros,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function montarAgendaDoDia(Usuario $paciente, ?string $dia = null): array
    {
        $diaEscolhido = $this->interpretarDia($dia);
        $concluidos = $this->registros->buscarIdsDeLembretesConcluidosNoDia($paciente, $diaEscolhido);

        return [
            'diaSelecionado' => $diaEscolhido->toDateString(),
            'ehHoje' => $diaEscolhido->isToday(),
            'mesDoDiaSelecionado' => $this->nomeDoMes($diaEscolhido),
            'semana' => $this->montarFaixaDaSemana($diaEscolhido),
            'lembretes' => $this->lembretes->buscarPorPaciente($paciente)
                ->map(fn ($lembrete): array => [
                    'id' => $lembrete->id,
                    'titulo' => $lembrete->titulo,
                    'tipo' => $lembrete->tipo->value,
                    'tipoRotulo' => $lembrete->tipo->rotulo(),
                    'horario' => $lembrete->horarioFormatado(),
                    'icone' => $lembrete->tipo->icone(),
                    'cor' => $lembrete->tipo->cor(),
                    'ativo' => $lembrete->ativo,
                    'concluido' => in_array($lembrete->id, $concluidos, strict: true),
                ])->values()->all(),
        ];
    }

    /**
     * Faixa de 7 dias começando no domingo da semana do dia escolhido, como no
     * calendário do protótipo.
     *
     * @return list<array<string, mixed>>
     */
    private function montarFaixaDaSemana(Carbon $diaEscolhido): array
    {
        $iniciais = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];
        $inicio = $diaEscolhido->copy()->startOfWeek(Carbon::SUNDAY);

        $semana = [];

        for ($posicao = 0; $posicao < 7; $posicao++) {
            $dia = $inicio->copy()->addDays($posicao);

            $semana[] = [
                'data' => $dia->toDateString(),
                'diaDoMes' => $dia->day,
                'inicial' => $iniciais[$posicao],
                'ehHoje' => $dia->isToday(),
                'noFuturo' => $dia->isFuture(),
            ];
        }

        return $semana;
    }

    /**
     * Aceita apenas datas válidas e não futuras; qualquer outra coisa cai em
     * hoje, para um parâmetro manipulado na URL não quebrar a tela.
     */
    private function interpretarDia(?string $dia): Carbon
    {
        if ($dia === null) {
            return Carbon::today();
        }

        try {
            $interpretado = Carbon::createFromFormat('Y-m-d', $dia)->startOfDay();
        } catch (\Throwable) {
            return Carbon::today();
        }

        return $interpretado->isFuture() ? Carbon::today() : $interpretado;
    }

    private function nomeDoMes(Carbon $dia): string
    {
        $meses = [
            'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO',
            'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO',
        ];

        return $meses[$dia->month - 1].' '.$dia->year;
    }
}
