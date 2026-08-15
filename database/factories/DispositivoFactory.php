<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Dispositivo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispositivo>
 */
class DispositivoFactory extends Factory
{
    protected $model = Dispositivo::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->words(2, true),
            'descricao' => fake()->sentence(),
            'icone' => fake()->randomElement(['Droplets', 'Waves', 'Circle', 'RefreshCw', 'ShieldCheck']),
            'cor' => fake()->randomElement(['teal', 'amber', 'coral', 'lavender']),
            'ativo' => true,
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn (array $atributos): array => ['ativo' => false]);
    }
}
