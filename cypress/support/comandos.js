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
    // Garante que não há sessão anterior. Sem isso, o /entrar redireciona
    // para a área do usuário já logado e o formulário nem aparece — o que
    // acontece, por exemplo, quando um hook before() deixou uma sessão aberta.
    cy.clearCookies();

    cy.visit('/entrar');
    cy.get('#email').clear().type(conta.email);
    cy.get('#password').clear().type(conta.senha, { log: false });
    cy.contains('button', 'Entrar').click();

    // Espera o redirecionamento terminar. Sem isso, um cy.visit() logo em
    // seguida aborta o POST do login e o teste segue sem sessão.
    cy.location('pathname').should('not.eq', '/entrar');
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

/**
 * Espera o servidor já responder com o texto na rota informada.
 *
 * As notificações são processadas por um worker de fila, então não estão
 * prontas no instante em que a equipe publica algo. Além disso, o Cypress roda
 * dentro da rede do compose e o WebSocket do Reverb aponta para o host do
 * desenvolvedor — o empurrão em tempo real não chega neste ambiente. Estes
 * testes verificam o caminho persistente (channel `database`); o caminho
 * WebSocket é verificado à parte (handshake 101 pelo nginx e publicação do
 * evento no Reverb).
 *
 * A espera acontece via cy.request (usando os cookies da sessão) em vez de
 * recarregar a página: recarregar em laço deixava o teste instável, porque a
 * verificação caía sobre um documento já substituído.
 */
Cypress.Commands.add('esperarNoServidor', (rota, texto, tentativasRestantes = 20) => {
    cy.request(rota).then((resposta) => {
        if (resposta.body.includes(texto)) {
            return;
        }

        if (tentativasRestantes <= 0) {
            throw new Error(`"${texto}" não chegou em ${rota} a tempo.`);
        }

        cy.wait(1000);
        cy.esperarNoServidor(rota, texto, tentativasRestantes - 1);
    });
});
