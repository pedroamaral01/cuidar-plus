<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrientacaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Instrução de cuidado cadastrada pela administração, sempre ligada a um
 * dispositivo. O conteúdo em si fica nas abas (TipoDeCuidado) e nos passos.
 */
#[Fillable(['dispositivo_id', 'titulo', 'subtitulo', 'publicada'])]
class Orientacao extends Model
{
    /** @use HasFactory<OrientacaoFactory> */
    use HasFactory;

    protected $table = 'orientacoes';

    protected function casts(): array
    {
        return ['publicada' => 'boolean'];
    }

    /** @return BelongsTo<Dispositivo, $this> */
    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }

    /** Abas da tela de detalhe (ex.: Troca da bolsa / Esvaziamento / Higiene). */
    /** @return HasMany<TipoDeCuidado, $this> */
    public function tiposDeCuidado(): HasMany
    {
        return $this->hasMany(TipoDeCuidado::class, 'orientacao_id')->orderBy('ordem');
    }

    /** @param Builder<Orientacao> $consulta */
    public function scopePublicadas(Builder $consulta): void
    {
        $consulta->where('publicada', true);
    }
}
