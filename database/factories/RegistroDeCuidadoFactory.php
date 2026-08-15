<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TipoDeLembrete;
use App\Models\RegistroDeCuidado;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistroDeCuidado>
 */
class RegistroDeCuidadoFactory extends Factory
{
    protected $model = RegistroDeCuidado::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(TipoDeLembrete::cases());

        return [
            'usuario_id' => Usuario::factory()->paciente(),
            'lembrete_id' => null,
            'titulo' => $tipo->rotulo().' realizada',
            'tipo' => $tipo,
            'observacao' => null,
            'realizado_em' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function realizadoEm(string $quando): static
    {
        return $this->state(fn (array $atributos): array => ['realizado_em' => $quando]);
    }
}
