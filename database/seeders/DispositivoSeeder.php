<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Dispositivo;
use App\Models\ItemDoPlanoDeCuidados;
use App\Models\PlanoDeCuidados;
use Illuminate\Database\Seeder;

/**
 * Dispositivos acompanhados pelo sistema e o plano de cuidados sugerido para
 * cada um — é o que o paciente revisa no passo 3 do cadastro.
 */
class DispositivoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->dispositivos() as $dados) {
            $dispositivo = Dispositivo::updateOrCreate(
                ['nome' => $dados['nome']],
                [
                    'descricao' => $dados['descricao'],
                    'icone' => $dados['icone'],
                    'cor' => $dados['cor'],
                    'ativo' => $dados['ativo'],
                ],
            );

            if ($dados['plano'] === []) {
                continue;
            }

            $this->criarPlanoDeCuidados($dispositivo, $dados['plano']);
        }
    }

    /**
     * @param  list<array{titulo: string, tipo: string, horario: string}>  $itens
     */
    private function criarPlanoDeCuidados(Dispositivo $dispositivo, array $itens): void
    {
        $plano = PlanoDeCuidados::updateOrCreate(
            ['dispositivo_id' => $dispositivo->id, 'nome' => 'Plano de cuidados — '.$dispositivo->nome],
            [
                'descricao' => 'Rotina sugerida para quem utiliza '.mb_strtolower($dispositivo->nome).'.',
                'ativo' => true,
            ],
        );

        foreach ($itens as $ordem => $item) {
            ItemDoPlanoDeCuidados::updateOrCreate(
                ['plano_de_cuidados_id' => $plano->id, 'titulo' => $item['titulo']],
                [
                    'tipo' => $item['tipo'],
                    'horario' => $item['horario'],
                    'ordem' => $ordem,
                ],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function dispositivos(): array
    {
        $planoDeBolsa = [
            ['titulo' => 'Trocar bolsa', 'tipo' => 'troca', 'horario' => '08:00'],
            ['titulo' => 'Higienizar o local', 'tipo' => 'higiene', 'horario' => '12:00'],
            ['titulo' => 'Esvaziar bolsa', 'tipo' => 'esvaziamento', 'horario' => '16:00'],
            ['titulo' => 'Proteger a pele', 'tipo' => 'protecao', 'horario' => '20:00'],
            ['titulo' => 'Medicamento', 'tipo' => 'medicamento', 'horario' => '21:00'],
        ];

        $planoDeSonda = [
            ['titulo' => 'Higienizar a região', 'tipo' => 'higiene', 'horario' => '08:00'],
            ['titulo' => 'Esvaziar bolsa coletora', 'tipo' => 'esvaziamento', 'horario' => '14:00'],
            ['titulo' => 'Trocar bolsa coletora', 'tipo' => 'troca', 'horario' => '20:00'],
        ];

        return [
            [
                'nome' => 'Colostomia',
                'descricao' => 'Abertura no abdome que desvia o trânsito do intestino grosso para uma bolsa coletora.',
                'icone' => 'Droplets',
                'cor' => 'teal',
                'ativo' => true,
                'plano' => $planoDeBolsa,
            ],
            [
                'nome' => 'Ileostomia',
                'descricao' => 'Abertura que desvia o trânsito do intestino delgado para uma bolsa coletora.',
                'icone' => 'Waves',
                'cor' => 'amber',
                'ativo' => true,
                'plano' => $planoDeBolsa,
            ],
            [
                'nome' => 'Cistostomia',
                'descricao' => 'Sonda que drena a urina diretamente da bexiga através do abdome.',
                'icone' => 'Circle',
                'cor' => 'lavender',
                'ativo' => true,
                'plano' => $planoDeSonda,
            ],
            [
                'nome' => 'Sonda vesical',
                'descricao' => 'Sonda que drena a urina da bexiga para uma bolsa coletora.',
                'icone' => 'RefreshCw',
                'cor' => 'teal',
                'ativo' => true,
                'plano' => $planoDeSonda,
            ],
            [
                'nome' => 'Sonda nasoenteral',
                'descricao' => 'Sonda usada para alimentação, posicionada do nariz até o intestino.',
                'icone' => 'ShieldCheck',
                'cor' => 'coral',
                'ativo' => true,
                'plano' => [
                    ['titulo' => 'Higienizar as narinas', 'tipo' => 'higiene', 'horario' => '07:00'],
                    ['titulo' => 'Lavar a sonda após a dieta', 'tipo' => 'higiene', 'horario' => '12:00'],
                    ['titulo' => 'Conferir a fixação', 'tipo' => 'protecao', 'horario' => '19:00'],
                ],
            ],
            [
                'nome' => 'Outros dispositivos',
                'descricao' => 'Outros dispositivos de saúde acompanhados pela equipe.',
                'icone' => 'MoreHorizontal',
                'cor' => 'lavender',
                'ativo' => true,
                'plano' => [],
            ],
        ];
    }
}
