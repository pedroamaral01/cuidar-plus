<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoDeLembrete;
use Database\Factories\LembreteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Cuidado agendado na rotina de um paciente. */
#[Fillable(['usuario_id', 'titulo', 'tipo', 'horario', 'ativo', 'notificado_em'])]
class Lembrete extends Model
{
    /** @use HasFactory<LembreteFactory> */
    use HasFactory;

    protected $table = 'lembretes';

    protected function casts(): array
    {
        return [
            'tipo' => TipoDeLembrete::class,
            'ativo' => 'boolean',
            'notificado_em' => 'datetime',
        ];
    }

    /** @return BelongsTo<Usuario, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /** @return HasMany<RegistroDeCuidado, $this> */
    public function registrosDeCuidados(): HasMany
    {
        return $this->hasMany(RegistroDeCuidado::class, 'lembrete_id');
    }

    /** @param Builder<Lembrete> $consulta */
    public function scopeAtivos(Builder $consulta): void
    {
        $consulta->where('ativo', true);
    }

    /** @param Builder<Lembrete> $consulta */
    public function scopeDoPaciente(Builder $consulta, int $usuarioId): void
    {
        $consulta->where('usuario_id', $usuarioId);
    }

    /** "08:00" — formato usado na interface. */
    public function horarioFormatado(): string
    {
        return substr((string) $this->horario, 0, 5);
    }
}
