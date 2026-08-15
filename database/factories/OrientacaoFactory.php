<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Dispositivo;
use App\Models\Orientacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Orientacao>
 */
class OrientacaoFactory extends Factory
{
    protected $model = Orientacao::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dispositivo_id' => Dispositivo::factory(),
            'titulo' => fake()->sentence(3),
            'subtitulo' => fake()->sentence(6),
            'publicada' => true,
        ];
    }

    public function despublicada(): static
    {
        return $this->state(fn (array $atributos): array => ['publicada' => false]);
    }
}
