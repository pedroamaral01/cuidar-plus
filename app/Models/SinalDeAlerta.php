<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GravidadeDoAlerta;
use Database\Factories\SinalDeAlertaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Situação que pode exigir atenção profissional. Quando `dispositivo_id` é
 * nulo, o sinal vale para pacientes de qualquer dispositivo.
 */
#[Fillable(['dispositivo_id', 'nome', 'orientacao', 'gravidade', 'publicado'])]
class SinalDeAlerta extends Model
{
    /** @use HasFactory<SinalDeAlertaFactory> */
    use HasFactory;

    protected $table = 'sinais_de_alerta';

    protected function casts(): array
    {
        return [
            'gravidade' => GravidadeDoAlerta::class,
            'publicado' => 'boolean',
        ];
    }

    /** @return BelongsTo<Dispositivo, $this> */
    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }

    /** @param Builder<SinalDeAlerta> $consulta */
    public function scopePublicados(Builder $consulta): void
    {
        $consulta->where('publicado', true);
    }

    /**
     * Sinais visíveis para quem usa um dispositivo: os específicos dele mais
     * os gerais (sem dispositivo).
     *
     * @param Builder<SinalDeAlerta> $consulta
     */
    public function scopeParaDispositivo(Builder $consulta, ?int $dispositivoId): void
    {
        $consulta->where(function (Builder $filtro) use ($dispositivoId): void {
            $filtro->whereNull('dispositivo_id');

            if ($dispositivoId !== null) {
                $filtro->orWhere('dispositivo_id', $dispositivoId);
            }
        });
    }
}
