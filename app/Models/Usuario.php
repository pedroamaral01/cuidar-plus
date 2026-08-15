<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PerfilDeUsuario;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Paciente ou administrador do sistema. A coluna `perfil` separa as duas
 * áreas da aplicação.
 */
#[Fillable([
    'nome',
    'email',
    'senha',
    'perfil',
    'data_de_nascimento',
    'cpf',
    'telefone',
    'cuidador_nome',
    'cuidador_telefone',
    'ativo',
])]
#[Hidden(['senha', 'remember_token'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'data_de_nascimento' => 'date',
            'senha' => 'hashed',
            'perfil' => PerfilDeUsuario::class,
            'ativo' => 'boolean',
        ];
    }

    /**
     * O Laravel procura a coluna `password` por padrão; aqui o domínio usa
     * `senha`. Este é um método de contrato do framework (Authenticatable).
     */
    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    // ------------------------------------------------------------------
    // Relacionamentos
    // ------------------------------------------------------------------

    /** @return BelongsToMany<Dispositivo, $this> */
    public function dispositivos(): BelongsToMany
    {
        return $this->belongsToMany(Dispositivo::class, 'usuarios_dispositivos', 'usuario_id', 'dispositivo_id')
            ->withPivot(['ativo', 'data_de_inicio'])
            ->withTimestamps();
    }

    /** @return HasMany<Lembrete, $this> */
    public function lembretes(): HasMany
    {
        return $this->hasMany(Lembrete::class, 'usuario_id');
    }

    /** @return HasMany<RegistroDeCuidado, $this> */
    public function registrosDeCuidados(): HasMany
    {
        return $this->hasMany(RegistroDeCuidado::class, 'usuario_id');
    }

    /** @return BelongsToMany<ConteudoEducativo, $this> */
    public function conteudosFavoritos(): BelongsToMany
    {
        return $this->belongsToMany(
            ConteudoEducativo::class,
            'usuarios_conteudos_favoritos',
            'usuario_id',
            'conteudo_educativo_id'
        )->withTimestamps();
    }

    // ------------------------------------------------------------------
    // Consultas e comportamentos do domínio
    // ------------------------------------------------------------------

    /** Dispositivo em uso hoje pelo paciente (o vínculo ativo mais recente). */
    public function dispositivoAtual(): ?Dispositivo
    {
        return $this->dispositivos()
            ->wherePivot('ativo', true)
            ->orderByPivot('id', 'desc')
            ->first();
    }

    public function ehAdministrador(): bool
    {
        return $this->perfil->ehAdministrador();
    }

    public function ehPaciente(): bool
    {
        return $this->perfil->ehPaciente();
    }

    /** @param Builder<Usuario> $consulta */
    public function scopePacientes(Builder $consulta): void
    {
        $consulta->where('perfil', PerfilDeUsuario::Paciente);
    }

    /** @param Builder<Usuario> $consulta */
    public function scopeAdministradores(Builder $consulta): void
    {
        $consulta->where('perfil', PerfilDeUsuario::Administrador);
    }
}
