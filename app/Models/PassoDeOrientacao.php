<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Um passo numerado do passo a passo de uma orientação. */
#[Fillable(['tipo_de_cuidado_id', 'descricao', 'ordem'])]
class PassoDeOrientacao extends Model
{
    protected $table = 'passos_de_orientacao';

    protected function casts(): array
    {
        return ['ordem' => 'integer'];
    }

    /** @return BelongsTo<TipoDeCuidado, $this> */
    public function tipoDeCuidado(): BelongsTo
    {
        return $this->belongsTo(TipoDeCuidado::class, 'tipo_de_cuidado_id');
    }
}
