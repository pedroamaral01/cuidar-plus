<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PerfilDeUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $senhaPadrao = null;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'senha' => static::$senhaPadrao ??= Hash::make('senha1234'),
            'perfil' => PerfilDeUsuario::Paciente,
            'data_de_nascimento' => fake()->dateTimeBetween('-85 years', '-20 years'),
            'telefone' => fake()->numerify('(##) 9####-####'),
            'ativo' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn (array $atributos): array => [
            'perfil' => PerfilDeUsuario::Administrador,
            'data_de_nascimento' => null,
            'cpf' => null,
        ]);
    }

    public function paciente(): static
    {
        return $this->state(fn (array $atributos): array => [
            'perfil' => PerfilDeUsuario::Paciente,
        ]);
    }

    public function comCuidador(): static
    {
        return $this->state(fn (array $atributos): array => [
            'cuidador_nome' => fake()->name(),
            'cuidador_telefone' => fake()->numerify('(##) 9####-####'),
        ]);
    }

    /** E-mail ainda não verificado. */
    public function naoVerificado(): static
    {
        return $this->state(fn (array $atributos): array => [
            'email_verified_at' => null,
        ]);
    }
}
