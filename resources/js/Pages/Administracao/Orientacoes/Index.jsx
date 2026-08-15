import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Selo from '@/Components/UI/Selo';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ orientacoes }) {
    const colunas = [
        { chave: 'titulo', rotulo: 'Título' },
        { chave: 'dispositivo', rotulo: 'Dispositivo' },
        { chave: 'tiposDeCuidado', rotulo: 'Tipos de cuidado' },
        { chave: 'atualizadoEm', rotulo: 'Atualizado em' },
        {
            chave: 'publicada',
            rotulo: 'Publicada',
            formatar: (linha) => (
                <Selo cor={linha.publicada ? 'teal' : 'coral'}>
                    {linha.publicada ? 'Sim' : 'Não'}
                </Selo>
            ),
        },
    ];

    return (
        <LayoutDoAplicativo titulo="Orientações" secaoAtiva="orientacoes">
            <AvisoDeStatus />

            <TabelaAdmin
                titulo="Orientações"
                subtitulo="Cada orientação tem abas (tipos de cuidado) com passo a passo numerado."
                colunas={colunas}
                linhas={orientacoes}
                rotaDeCriacao={route('administracao.orientacoes.create')}
                rotuloDoBotao="Nova orientação"
                rotaDeEdicao={(linha) =>
                    route('administracao.orientacoes.edit', linha.id)
                }
                rotaDeExclusao={(linha) =>
                    route('administracao.orientacoes.destroy', linha.id)
                }
                descreverLinha={(linha) => linha.titulo}
                vazio="Nenhuma orientação cadastrada."
            />
        </LayoutDoAplicativo>
    );
}
