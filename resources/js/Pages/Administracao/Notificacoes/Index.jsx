import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import Selo from '@/Components/UI/Selo';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ notificacoes }) {
    const colunas = [
        { chave: 'destinatario', rotulo: 'Destinatário' },
        { chave: 'tipo', rotulo: 'Tipo' },
        { chave: 'titulo', rotulo: 'Título' },
        { chave: 'canal', rotulo: 'Canal' },
        { chave: 'enviadoEm', rotulo: 'Enviado em' },
        {
            chave: 'lida',
            rotulo: 'Lida',
            formatar: (linha) => (
                <Selo cor={linha.lida ? 'teal' : 'amber'}>
                    {linha.lida ? 'Sim' : 'Não'}
                </Selo>
            ),
        },
    ];

    return (
        <LayoutDoAplicativo
            titulo="Notificações enviadas"
            secaoAtiva="notificacoes"
        >
            <TabelaAdmin
                titulo="Notificações enviadas"
                subtitulo="Auditoria das últimas 100 notificações disparadas pelo sistema."
                colunas={colunas}
                linhas={notificacoes}
                vazio="Nenhuma notificação enviada até agora."
            />
        </LayoutDoAplicativo>
    );
}
