import TabelaAdmin from '@/Components/Administracao/TabelaAdmin';
import Selo from '@/Components/UI/Selo';
import LayoutDoAplicativo from '@/Layouts/LayoutDoAplicativo';

export default function Index({ pacientes }) {
    const colunas = [
        { chave: 'nome', rotulo: 'Nome' },
        { chave: 'email', rotulo: 'E-mail' },
        { chave: 'dispositivo', rotulo: 'Dispositivo' },
        { chave: 'cadastroEm', rotulo: 'Cadastro' },
        { chave: 'registros', rotulo: 'Cuidados registrados' },
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
        <LayoutDoAplicativo titulo="Pacientes" secaoAtiva="pacientes">
            {/* Sem ações de edição: o MVP prevê apenas consulta (seção 2.3). */}
            <TabelaAdmin
                titulo="Pacientes"
                subtitulo="Visualização dos pacientes cadastrados. O conteúdo do diário de cada um permanece privado."
                colunas={colunas}
                linhas={pacientes}
                vazio="Nenhum paciente cadastrado."
            />
        </LayoutDoAplicativo>
    );
}
