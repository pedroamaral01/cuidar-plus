/**
 * Comandos reutilizados pelos testes end-to-end.
 */

/** Contas criadas pelo seeder de demonstração. */
export const CONTAS = {
    paciente: { email: 'maria@cuidarplus.local', senha: 'senha1234', nome: 'Maria' },
    outroPaciente: { email: 'joao@cuidarplus.local', senha: 'senha1234', nome: 'João' },
    administrador: { email: 'admin@cuidarplus.local', senha: 'senha1234', nome: 'Equipe' },
};

Cypress.Commands.add('entrarComo', (conta) => {
    cy.visit('/entrar');
    cy.get('#email').clear().type(conta.email);
    cy.get('#password').clear().type(conta.senha, { log: false });
    cy.contains('button', 'Entrar').click();
});

Cypress.Commands.add('sair', () => {
    cy.contains('button', 'Sair').click();
    cy.location('pathname').should('eq', '/entrar');
});

/**
 * E-mail único por execução, para o cadastro poder rodar quantas vezes
 * precisar sem esbarrar na regra de e-mail já usado.
 */
Cypress.Commands.add('emailDeTeste', () => {
    return cy.wrap(`paciente.teste.${Date.now()}@exemplo.com`, { log: false });
});
