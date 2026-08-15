<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\GravidadeDoAlerta;
use App\Models\SinalDeAlerta;
use Illuminate\Database\Seeder;

/**
 * Sinais de alerta gerais (sem dispositivo), visíveis para todos os pacientes.
 */
class SinalDeAlertaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sinais() as $dados) {
            SinalDeAlerta::updateOrCreate(
                ['dispositivo_id' => null, 'nome' => $dados['nome']],
                [
                    'orientacao' => $dados['orientacao'],
                    'gravidade' => $dados['gravidade'],
                    'publicado' => true,
                ],
            );
        }
    }

    /**
     * @return list<array{nome: string, orientacao: string, gravidade: GravidadeDoAlerta}>
     */
    private function sinais(): array
    {
        return [
            [
                'nome' => 'Sangramento',
                'orientacao' => 'Se o sangramento for intenso ou não parar, procure atendimento imediatamente.',
                'gravidade' => GravidadeDoAlerta::Alta,
            ],
            [
                'nome' => 'Dor persistente',
                'orientacao' => 'Dor que não melhora com o cuidado habitual pode indicar complicação. Entre em contato com a equipe.',
                'gravidade' => GravidadeDoAlerta::Alta,
            ],
            [
                'nome' => 'Febre',
                'orientacao' => 'Febre associada a outros sintomas exige atenção da equipe de saúde. Meça a temperatura e registre.',
                'gravidade' => GravidadeDoAlerta::Alta,
            ],
            [
                'nome' => 'Vazamento',
                'orientacao' => 'Vazamentos frequentes podem indicar necessidade de ajuste no dispositivo. Informe a equipe.',
                'gravidade' => GravidadeDoAlerta::Alta,
            ],
            [
                'nome' => 'Vermelhidão',
                'orientacao' => 'Vermelhidão ao redor do dispositivo pode ser sinal de irritação ou infecção. Observe se aumenta.',
                'gravidade' => GravidadeDoAlerta::Media,
            ],
            [
                'nome' => 'Inchaço',
                'orientacao' => 'Observe se o inchaço aumenta ao longo do dia e informe a equipe na próxima consulta.',
                'gravidade' => GravidadeDoAlerta::Media,
            ],
            [
                'nome' => 'Alteração nas fezes ou urina',
                'orientacao' => 'Mudanças de cor, cheiro ou consistência devem ser registradas no diário e informadas à equipe.',
                'gravidade' => GravidadeDoAlerta::Media,
            ],
        ];
    }
}
