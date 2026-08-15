import { CONTAS } from '../support/comandos';

const CENTRAL = '/inicio/notificacoes';
const AVISO_DE_ALERTA = 'Novo sinal de alerta cadastrado';

describe('Notificações', () => {
    const nomeDoSinal = `Sinal notificado ${Date.now()}`;

    /**
     * Publica os sinais de alerta que geram as notificações deste arquivo.
     *
     * São dois de propósito: um teste marca uma notificação como lida e outro
     * marca todas. Com um único aviso, o segundo teste encontraria a caixa já
     * vazia e nem acharia o botão "Marcar todas como lidas".
     */
    before(() => {
        cy.entrarComo(CONTAS.administrador);

        [nomeDoSinal, `${nomeDoSinal} (segundo)`].forEach((nome) => {
            cy.visit('/administracao/alertas/create');

            cy.get('#nome').type(nome);
            cy.get('#orientacao').type('Procure a equipe se o sintoma persistir.');
            cy.get('#gravidade').select('alta');
            cy.contains('button', 'Salvar').click();

            cy.contains('Os pacientes foram avisados.').should('be.visible');
        });
    });

    it('o paciente recebe o aviso do novo sinal de alerta', () => {
        cy.entrarComo(CONTAS.paciente);
        cy.esperarNoServidor(CENTRAL, AVISO_DE_ALERTA);

        cy.visit(CENTRAL);
        cy.contains(AVISO_DE_ALERTA).should('be.visible');
        cy.contains(nomeDoSinal).should('be.visible');
    });

    it('marca uma notificação como lida e ela leva para a tela do assunto', () => {
        cy.entrarComo(CONTAS.paciente);
        cy.esperarNoServidor(CENTRAL, AVISO_DE_ALERTA);

        cy.visit(CENTRAL);

        cy.get('[aria-label$="não lida"]')
            .its('length')
            .then((antes) => {
                cy.get('[aria-label$="não lida"]').first().click();

                // A notificação de sinal de alerta abre a tela de alertas.
                cy.location('pathname').should('eq', '/inicio/sinais-de-alerta');

                cy.visit(CENTRAL);
                cy.get('[aria-label$="não lida"]').should('have.length', antes - 1);
            });
    });

    it('marca todas as notificações como lidas', () => {
        cy.entrarComo(CONTAS.paciente);
        cy.esperarNoServidor(CENTRAL, AVISO_DE_ALERTA);

        cy.visit(CENTRAL);
        cy.contains('button', 'Marcar todas como lidas').click();

        cy.contains('Todas as notificações foram marcadas como lidas.').should(
            'be.visible',
        );
        cy.get('[aria-label$="não lida"]').should('not.exist');
        cy.contains('button', 'Marcar todas como lidas').should('not.exist');
    });

    it('cada paciente enxerga a própria cópia da notificação', () => {
        // A Maria já leu as dela no teste anterior; o João continua com as
        // suas não lidas — as cópias são independentes.
        cy.entrarComo(CONTAS.outroPaciente);
        cy.esperarNoServidor(CENTRAL, AVISO_DE_ALERTA);

        cy.visit(CENTRAL);
        cy.get('[aria-label$="não lida"]').should('have.length.at.least', 1);
    });

    it('a tela abre vazia para quem nunca recebeu nada', () => {
        cy.emailDeTeste().then((email) => {
            cy.visit('/cadastro');

            cy.get('#nome').type('Paciente Novo');
            cy.get('#email').type(email);
            cy.get('#password').type('senha1234');
            cy.get('#password_confirmation').type('senha1234');
            cy.contains('button', 'Continuar').click();

            cy.contains('button', 'Colostomia').click();
            cy.contains('button', 'Continuar').click();
            cy.contains('button', 'Concluir cadastro').click();

            cy.location('pathname').should('eq', '/inicio');

            cy.visit(CENTRAL);
            cy.contains('Você ainda não tem notificações').should('be.visible');
        });
    });
});
