import {
    AlertTriangle,
    Bell,
    BookOpen,
    Calendar,
    ClipboardList,
    Film,
    Home,
    LayoutDashboard,
    MessageCircle,
    Package,
    User,
    Users,
} from 'lucide-react';

/**
 * Itens de navegação de cada perfil.
 *
 * `rota` é o nome da rota Laravel. O Shell filtra os itens cuja rota ainda não
 * existe (`route().has(...)`), então a navegação sempre reflete o que está de
 * fato implementado — nunca há link morto na interface.
 */
export const navegacaoDoPaciente = [
    { id: 'inicio', rotulo: 'Início', rota: 'paciente.inicio', Icone: Home },
    { id: 'dispositivo', rotulo: 'Meu dispositivo', rota: 'paciente.dispositivo', Icone: Package, cor: 'teal' },
    { id: 'orientacoes', rotulo: 'Orientações', rota: 'paciente.orientacoes.index', Icone: BookOpen, cor: 'lavender' },
    { id: 'alertas', rotulo: 'Sinais de alerta', rota: 'paciente.alertas', Icone: AlertTriangle, cor: 'coral' },
    { id: 'lembretes', rotulo: 'Lembretes', rota: 'paciente.lembretes.index', Icone: Calendar, cor: 'amber' },
    { id: 'diario', rotulo: 'Diário', rota: 'paciente.diario', Icone: ClipboardList, cor: 'teal' },
    { id: 'conteudos', rotulo: 'Conteúdos', rota: 'paciente.conteudos', Icone: Film, cor: 'amber' },
    { id: 'notificacoes', rotulo: 'Notificações', rota: 'paciente.notificacoes', Icone: Bell, cor: 'teal' },
    { id: 'equipe', rotulo: 'Falar com a equipe', rota: 'paciente.equipe', Icone: MessageCircle, cor: 'lavender' },
    { id: 'perfil', rotulo: 'Meu perfil', rota: 'paciente.perfil', Icone: User, cor: 'teal' },
];

export const navegacaoDoAdministrador = [
    { id: 'painel', rotulo: 'Painel', rota: 'administracao.painel', Icone: LayoutDashboard },
    { id: 'pacientes', rotulo: 'Pacientes', rota: 'administracao.pacientes.index', Icone: Users, cor: 'teal' },
    { id: 'dispositivos', rotulo: 'Dispositivos', rota: 'administracao.dispositivos.index', Icone: Package, cor: 'teal' },
    { id: 'orientacoes', rotulo: 'Orientações', rota: 'administracao.orientacoes.index', Icone: BookOpen, cor: 'lavender' },
    { id: 'alertas', rotulo: 'Sinais de alerta', rota: 'administracao.alertas.index', Icone: AlertTriangle, cor: 'coral' },
    { id: 'conteudos', rotulo: 'Conteúdos', rota: 'administracao.conteudos.index', Icone: Film, cor: 'amber' },
    { id: 'notificacoes', rotulo: 'Notificações enviadas', rota: 'administracao.notificacoes', Icone: Bell, cor: 'lavender' },
];

/**
 * No celular a barra inferior mostra 4 itens fixos + "Mais". Estes são os ids
 * dos 4 fixos; todo o resto vai para o painel "Mais".
 */
export const fixosNaBarraInferior = {
    paciente: ['inicio', 'lembretes', 'diario', 'notificacoes'],
    administrador: ['painel', 'pacientes', 'dispositivos', 'notificacoes'],
};

export function navegacaoDoPerfil(perfil) {
    return perfil === 'administrador'
        ? navegacaoDoAdministrador
        : navegacaoDoPaciente;
}
