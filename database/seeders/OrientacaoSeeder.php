<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Dispositivo;
use App\Models\Orientacao;
use App\Models\PassoDeOrientacao;
use App\Models\TipoDeCuidado;
use Illuminate\Database\Seeder;

/**
 * Orientações de cuidado, com abas (tipos de cuidado) e passo a passo
 * numerado — conteúdo mantido pela administração, nunca fixo no React.
 */
class OrientacaoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->orientacoes() as $dados) {
            $dispositivo = Dispositivo::where('nome', $dados['dispositivo'])->first();

            if ($dispositivo === null) {
                continue;
            }

            $orientacao = Orientacao::updateOrCreate(
                ['dispositivo_id' => $dispositivo->id, 'titulo' => $dados['titulo']],
                ['subtitulo' => $dados['subtitulo'], 'publicada' => true],
            );

            foreach ($dados['abas'] as $ordemDaAba => $aba) {
                $tipoDeCuidado = TipoDeCuidado::updateOrCreate(
                    ['orientacao_id' => $orientacao->id, 'nome' => $aba['nome']],
                    ['ordem' => $ordemDaAba],
                );

                foreach ($aba['passos'] as $ordemDoPasso => $descricao) {
                    PassoDeOrientacao::updateOrCreate(
                        ['tipo_de_cuidado_id' => $tipoDeCuidado->id, 'ordem' => $ordemDoPasso],
                        ['descricao' => $descricao],
                    );
                }
            }
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function orientacoes(): array
    {
        return [
            [
                'dispositivo' => 'Colostomia',
                'titulo' => 'Colostomia',
                'subtitulo' => 'Aprenda como realizar os cuidados corretamente.',
                'abas' => [
                    [
                        'nome' => 'Troca da bolsa',
                        'passos' => [
                            'Reúna todo o material antes de começar.',
                            'Retire a bolsa com cuidado, de cima para baixo.',
                            'Limpe a pele ao redor com água morna.',
                            'Seque bem antes de aplicar a nova bolsa.',
                            'Fixe a nova bolsa alinhada ao estoma.',
                        ],
                    ],
                    [
                        'nome' => 'Esvaziamento',
                        'passos' => [
                            'Posicione a extremidade da bolsa sobre o vaso sanitário.',
                            'Abra a válvula de esvaziamento.',
                            'Limpe a extremidade após esvaziar.',
                            'Feche bem a válvula.',
                        ],
                    ],
                    [
                        'nome' => 'Higiene',
                        'passos' => [
                            'Lave as mãos antes e depois do procedimento.',
                            'Utilize água morna e gaze macia.',
                            'Evite produtos com álcool ou perfume na pele ao redor do estoma.',
                        ],
                    ],
                ],
            ],
            [
                'dispositivo' => 'Ileostomia',
                'titulo' => 'Ileostomia',
                'subtitulo' => 'O efluente é mais líquido — a proteção da pele exige atenção extra.',
                'abas' => [
                    [
                        'nome' => 'Troca da bolsa',
                        'passos' => [
                            'Separe o material e lave bem as mãos.',
                            'Remova a bolsa apoiando a pele com a outra mão.',
                            'Limpe a pele apenas com água morna, sem esfregar.',
                            'Aplique a barreira protetora antes da nova bolsa.',
                            'Confira se a abertura está no tamanho do estoma.',
                        ],
                    ],
                    [
                        'nome' => 'Esvaziamento',
                        'passos' => [
                            'Esvazie sempre que a bolsa estiver com cerca de um terço.',
                            'Evite deixar encher demais para não soltar a placa.',
                            'Limpe e feche bem a válvula ao terminar.',
                        ],
                    ],
                    [
                        'nome' => 'Hidratação',
                        'passos' => [
                            'Beba líquidos ao longo de todo o dia.',
                            'Observe sinais de desidratação, como boca seca e urina escura.',
                            'Informe a equipe se o volume aumentar muito.',
                        ],
                    ],
                ],
            ],
            [
                'dispositivo' => 'Sonda vesical',
                'titulo' => 'Sonda vesical',
                'subtitulo' => 'Cuidados diários para evitar infecções.',
                'abas' => [
                    [
                        'nome' => 'Troca da bolsa coletora',
                        'passos' => [
                            'Higienize as mãos antes de manusear.',
                            'Esvazie a bolsa coletora antes da troca.',
                            'Conecte a nova bolsa sem tocar nas conexões.',
                        ],
                    ],
                    [
                        'nome' => 'Higiene',
                        'passos' => [
                            'Limpe a região com água e sabão neutro diariamente.',
                            'Evite tracionar a sonda durante a limpeza.',
                        ],
                    ],
                ],
            ],
            [
                'dispositivo' => 'Sonda nasoenteral',
                'titulo' => 'Sonda nasoenteral',
                'subtitulo' => 'Alimentação segura e cuidados com a fixação.',
                'abas' => [
                    [
                        'nome' => 'Administração da dieta',
                        'passos' => [
                            'Mantenha a cabeceira elevada durante e após a dieta.',
                            'Confira a marcação da sonda antes de iniciar.',
                            'Administre a dieta lentamente, conforme orientado.',
                            'Lave a sonda com água ao final.',
                        ],
                    ],
                    [
                        'nome' => 'Cuidados com a fixação',
                        'passos' => [
                            'Troque a fixação sempre que estiver solta ou suja.',
                            'Observe a pele do nariz para evitar feridas.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
