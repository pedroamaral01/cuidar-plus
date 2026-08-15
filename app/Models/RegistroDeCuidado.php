<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoDeLembrete;
use Database\Factories\RegistroDeCuidadoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Cuidado efetivamente realizado — alimenta o diário/histórico do paciente. */
#[Fillable(['usuario_id', 'lembrete_id', 'titulo', 'tipo', 'observacao', 'realizado_em'])]
class RegistroDeCuidado extends Model
{
    /** @use HasFactory<RegistroDeCuidadoFactory> */
    use HasFactory;

    protected $table = 'registros_de_cuidados';

    protected function casts(): array
    {
        return [
            'tipo' => TipoDeLembrete::class,
            'realizado_em' => 'datetime',
        ];
    }

    /** @return BelongsTo<Usuario, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /** @return BelongsTo<Lembrete, $this> */
    public function lembrete(): BelongsTo
    {
        return $this->belongsTo(Lembrete::class, 'lembrete_id');
    }

    /** @param Builder<RegistroDeCuidado> $consulta */
    public function scopeDoPaciente(Builder $consulta, int $usuarioId): void
    {
        $consulta->where('usuario_id', $usuarioId);
    }

    /** @param Builder<RegistroDeCuidado> $consulta */
    public function scopeMaisRecentesPrimeiro(Builder $consulta): void
    {
        $consulta->orderByDesc('realizado_em');
    }
}
