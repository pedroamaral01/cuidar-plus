<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoDeLembrete;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Um cuidado do plano (ex.: "Trocar bolsa às 08:00"). Vira um Lembrete do
 * paciente quando ele conclui o cadastro.
 */
#[Fillable(['plano_de_cuidados_id', 'titulo', 'tipo', 'horario', 'ordem'])]
class ItemDoPlanoDeCuidados extends Model
{
    protected $table = 'itens_do_plano_de_cuidados';

    protected function casts(): array
    {
        return [
            'tipo' => TipoDeLembrete::class,
            'ordem' => 'integer',
        ];
    }

    /** @return BelongsTo<PlanoDeCuidados, $this> */
    public function planoDeCuidados(): BelongsTo
    {
        return $this->belongsTo(PlanoDeCuidados::class, 'plano_de_cuidados_id');
    }
}
