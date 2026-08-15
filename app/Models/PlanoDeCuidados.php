<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlanoDeCuidadosFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Conjunto de cuidados sugerido para um dispositivo. É o que o paciente revisa
 * no passo 3 do cadastro e o que dá origem aos seus lembretes iniciais.
 */
#[Fillable(['dispositivo_id', 'nome', 'descricao', 'ativo'])]
class PlanoDeCuidados extends Model
{
    /** @use HasFactory<PlanoDeCuidadosFactory> */
    use HasFactory;

    protected $table = 'planos_de_cuidados';

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    /** @return BelongsTo<Dispositivo, $this> */
    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }

    /** @return HasMany<ItemDoPlanoDeCuidados, $this> */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemDoPlanoDeCuidados::class, 'plano_de_cuidados_id')->orderBy('ordem');
    }

    /** @param Builder<PlanoDeCuidados> $consulta */
    public function scopeAtivos(Builder $consulta): void
    {
        $consulta->where('ativo', true);
    }
}
