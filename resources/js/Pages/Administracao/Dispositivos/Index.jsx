import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import IconeDinamico from '@/Components/UI/IconeDinamico';
import Selo from '@/Components/UI/Selo';
import { classesDaCor } from '@/Components/UI/paleta';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ dispositivos }) {
    const colunas = [
        {
            chave: 'nome',
            rotulo: 'Nome',
            formatar: (linha) => (
                <span className="flex items-center gap-2">
                    <span
                        className={`flex h-7 w-7 items-center justify-center rounded-full ${classesDaCor(linha.cor).fundo}`}
                    >
                        <IconeDinamico
                            nome={linha.icone}
                            size={13}
                            className={classesDaCor(linha.cor).texto}
                        />
                    </span>
                    {linha.nome}
                </span>
            ),
        },
        { chave: 'pacientesVinculados', rotulo: 'Pacientes vinculados' },
        {
            chave: 'ativo',
            rotulo: 'Status',
            formatar: (linha) => (
                <Selo cor={linha.ativo ? 'teal' : 'coral'}>
                    {linha.ativo ? 'Ativo' : 'Inativo'}
                </Selo>
            ),
        },
    ];

    return (
        <LayoutDoAplicativo titulo="Dispositivos" secaoAtiva="dispositivos">
            <AvisoDeStatus />

            <TabelaAdmin
                titulo="Dispositivos"
                subtitulo="Cadastro, edição, ativação e desativação de dispositivos."
                colunas={colunas}
                linhas={dispositivos}
                rotaDeCriacao={route('administracao.dispositivos.create')}
                rotuloDoBotao="Novo dispositivo"
                rotaDeEdicao={(linha) =>
                    route('administracao.dispositivos.edit', linha.id)
                }
                rotaDeExclusao={(linha) =>
                    route('administracao.dispositivos.destroy', linha.id)
                }
                vazio="Nenhum dispositivo cadastrado."
            />
        </LayoutDoAplicativo>
    );
}
