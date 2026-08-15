<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoDeConteudo;
use Database\Factories\ConteudoEducativoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Texto ou vídeo educativo publicado pela administração. */
#[Fillable(['dispositivo_id', 'titulo', 'resumo', 'corpo', 'tipo', 'url_do_video', 'publicado'])]
class ConteudoEducativo extends Model
{
    /** @use HasFactory<ConteudoEducativoFactory> */
    use HasFactory;

    protected $table = 'conteudos_educativos';

    protected function casts(): array
    {
        return [
            'tipo' => TipoDeConteudo::class,
            'publicado' => 'boolean',
        ];
    }

    /** @return BelongsTo<Dispositivo, $this> */
    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }

    /** @return BelongsToMany<Usuario, $this> */
    public function favoritadoPor(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuarios_conteudos_favoritos',
            'conteudo_educativo_id',
            'usuario_id'
        )->withTimestamps();
    }

    /** @param Builder<ConteudoEducativo> $consulta */
    public function scopePublicados(Builder $consulta): void
    {
        $consulta->where('publicado', true);
    }

    /**
     * Conteúdos do dispositivo do paciente mais os gerais.
     *
     * @param Builder<ConteudoEducativo> $consulta
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
