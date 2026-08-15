import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import AvisoDeStatus from '@/Components/UI/AvisoDeStatus';
import Selo from '@/Components/UI/Selo';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ sinais }) {
    const colunas = [
        { chave: 'nome', rotulo: 'Nome' },
        {
            chave: 'gravidade',
            rotulo: 'Gravidade',
            formatar: (linha) => (
                <Selo cor={linha.gravidadeCor}>{linha.gravidadeRotulo}</Selo>
            ),
        },
        { chave: 'dispositivo', rotulo: 'Dispositivo' },
        {
            chave: 'publicado',
            rotulo: 'Publicado',
            formatar: (linha) => (
                <Selo cor={linha.publicado ? 'teal' : 'coral'}>
                    {linha.publicado ? 'Sim' : 'Não'}
                </Selo>
            ),
        },
    ];

    return (
        <LayoutDoAplicativo titulo="Sinais de alerta" secaoAtiva="alertas">
            <AvisoDeStatus />

            <TabelaAdmin
                titulo="Sinais de alerta"
                subtitulo="Situações que podem exigir atenção profissional. Sem dispositivo, o sinal vale para todos os pacientes."
                colunas={colunas}
                linhas={sinais}
                rotaDeCriacao={route('administracao.alertas.create')}
                rotuloDoBotao="Novo sinal"
                rotaDeEdicao={(linha) => route('administracao.alertas.edit', linha.id)}
                rotaDeExclusao={(linha) =>
                    route('administracao.alertas.destroy', linha.id)
                }
                vazio="Nenhum sinal de alerta cadastrado."
            />
        </LayoutDoAplicativo>
    );
}
