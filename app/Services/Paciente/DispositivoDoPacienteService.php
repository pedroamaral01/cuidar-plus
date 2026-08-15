<?php

declare(strict_types=1);

namespace App\Services\Paciente;

use App\Models\Dispositivo;
use App\Models\Lembrete;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

/**
 * Vínculo entre paciente e dispositivo, e a criação dos lembretes iniciais a
 * partir do plano de cuidados.
 */
class DispositivoDoPacienteService
{
    /**
     * Define o dispositivo em uso pelo paciente. O vínculo anterior é
     * desativado (e não apagado), preservando o histórico.
     */
    public function definirDispositivo(Usuario $paciente, Dispositivo $dispositivo, bool $aplicarPlano = true): void
    {
        DB::transaction(function () use ($paciente, $dispositivo, $aplicarPlano): void {
            $paciente->dispositivos()->newPivotQuery()->update(['ativo' => false]);

            $paciente->dispositivos()->syncWithoutDetaching([
                $dispositivo->id => ['ativo' => true, 'data_de_inicio' => now()->toDateString()],
            ]);

            if ($aplicarPlano) {
                $this->aplicarPlanoDeCuidados($paciente, $dispositivo);
            }
        });
    }

    /**
     * Transforma os itens do plano de cuidados do dispositivo nos lembretes do
     * paciente. Não duplica lembretes que o paciente já tenha com o mesmo
     * título — trocar de dispositivo não deve bagunçar a rotina existente.
     */
    public function aplicarPlanoDeCuidados(Usuario $paciente, Dispositivo $dispositivo): int
    {
        $plano = $dispositivo->planosDeCuidados()->where('ativo', true)->with('itens')->first();

        if ($plano === null) {
            return 0;
        }

        $titulosExistentes = $paciente->lembretes()->pluck('titulo')->all();
        $criados = 0;

        foreach ($plano->itens as $item) {
            if (in_array($item->titulo, $titulosExistentes, strict: true)) {
                continue;
            }

            Lembrete::create([
                'usuario_id' => $paciente->id,
                'titulo' => $item->titulo,
                'tipo' => $item->tipo,
                'horario' => $item->horario,
                'ativo' => true,
            ]);

            $criados++;
        }

        return $criados;
    }
}
