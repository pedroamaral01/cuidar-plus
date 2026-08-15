<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Uma aba dentro de uma orientação. Agrupa o passo a passo de um tipo
 * específico de cuidado.
 */
#[Fillable(['orientacao_id', 'nome', 'ordem'])]
class TipoDeCuidado extends Model
{
    protected $table = 'tipos_de_cuidado';

    protected function casts(): array
    {
        return ['ordem' => 'integer'];
    }

    /** @return BelongsTo<Orientacao, $this> */
    public function orientacao(): BelongsTo
    {
        return $this->belongsTo(Orientacao::class, 'orientacao_id');
    }

    /** @return HasMany<PassoDeOrientacao, $this> */
    public function passos(): HasMany
    {
        return $this->hasMany(PassoDeOrientacao::class, 'tipo_de_cuidado_id')->orderBy('ordem');
    }
}
