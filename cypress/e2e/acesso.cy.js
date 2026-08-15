import { CONTAS } from '../support/comandos';

describe('Acesso ao sistema', () => {
    it('paciente entra e chega ao próprio início', () => {
        cy.entrarComo(CONTAS.paciente);

        cy.location('pathname').should('eq', '/inicio');
        cy.contains(`Olá, ${CONTAS.paciente.nome}`).should('be.visible');
        cy.contains('Cuidados realizados esta semana').should('be.visible');
    });

    it('administrador entra e chega à área administrativa', () => {
        cy.entrarComo(CONTAS.administrador);

        cy.location('pathname').should('eq', '/administracao');
        cy.contains('Pacientes ativos').should('be.visible');
    });

    it('senha incorreta mostra erro e não autentica', () => {
        cy.visit('/entrar');
        cy.get('#email').type(CONTAS.paciente.email);
        cy.get('#password').type('senha-errada');
        cy.contains('button', 'Entrar').click();

        cy.contains('E-mail ou senha incorretos.').should('be.visible');
        cy.location('pathname').should('eq', '/entrar');
    });

    it('visitante é levado ao acesso ao tentar abrir área protegida', () => {
        cy.visit('/inicio');

        cy.location('pathname').should('eq', '/entrar');
    });

    it('paciente consegue sair da conta', () => {
        cy.entrarComo(CONTAS.paciente);
        cy.sair();

        // A sessão realmente acabou: a área protegida volta a redirecionar.
        cy.visit('/inicio');
        cy.location('pathname').should('eq', '/entrar');
    });
});
