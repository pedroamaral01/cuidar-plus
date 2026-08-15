<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Usuario;

/**
 * Monta o painel inicial do paciente: saudação, resumo semanal e o que ele
 * precisa ver primeiro.
 */
class PainelDoPacienteService
{
    public function __construct(
        private readonly RegistroDeCuidadoService $registrosDeCuidados,
        private readonly LembreteService $lembretes,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function montarPainel(Usuario $paciente): array
    {
        $dispositivo = $paciente->dispositivoAtual();
        $resumo = $this->registrosDeCuidados->montarResumoDaSemana($paciente);

        return [
            'primeiroNome' => $this->extrairPrimeiroNome($paciente->nome),
            'dispositivo' => $dispositivo === null ? null : [
                'id' => $dispositivo->id,
                'nome' => $dispositivo->nome,
                'icone' => $dispositivo->icone,
                'cor' => $dispositivo->cor,
            ],
            'resumoDaSemana' => $resumo->paraArray(),
            'naoLidas' => $paciente->unreadNotifications()->count(),
            'lembretesDeHoje' => $this->lembretes->buscarLembretesDoPaciente($paciente)
                ->where('ativo', true)
                ->map(fn ($lembrete): array => [
                    'id' => $lembrete->id,
                    'titulo' => $lembrete->titulo,
                    'horario' => $lembrete->horarioFormatado(),
                    'icone' => $lembrete->tipo->icone(),
                    'cor' => $lembrete->tipo->cor(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function extrairPrimeiroNome(string $nome): string
    {
        return explode(' ', trim($nome))[0];
    }
}
