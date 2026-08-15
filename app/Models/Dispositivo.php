<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DispositivoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tipo de dispositivo de saúde acompanhado pelo sistema
 * (colostomia, ileostomia, sonda vesical...).
 */
#[Fillable(['nome', 'descricao', 'icone', 'cor', 'ativo'])]
class Dispositivo extends Model
{
    /** @use HasFactory<DispositivoFactory> */
    use HasFactory;

    protected $table = 'dispositivos';

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    /** @return BelongsToMany<Usuario, $this> */
    public function pacientes(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_dispositivos', 'dispositivo_id', 'usuario_id')
            ->withPivot(['ativo', 'data_de_inicio'])
            ->withTimestamps();
    }

    /** @return HasMany<Orientacao, $this> */
    public function orientacoes(): HasMany
    {
        return $this->hasMany(Orientacao::class, 'dispositivo_id');
    }

    /** @return HasMany<SinalDeAlerta, $this> */
    public function sinaisDeAlerta(): HasMany
    {
        return $this->hasMany(SinalDeAlerta::class, 'dispositivo_id');
    }

    /** @return HasMany<PlanoDeCuidados, $this> */
    public function planosDeCuidados(): HasMany
    {
        return $this->hasMany(PlanoDeCuidados::class, 'dispositivo_id');
    }

    /** @return HasMany<ConteudoEducativo, $this> */
    public function conteudosEducativos(): HasMany
    {
        return $this->hasMany(ConteudoEducativo::class, 'dispositivo_id');
    }

    /** @param Builder<Dispositivo> $consulta */
    public function scopeAtivos(Builder $consulta): void
    {
        $consulta->where('ativo', true);
    }
}
