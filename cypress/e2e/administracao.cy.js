import { CONTAS } from '../support/comandos';

describe('Área administrativa', () => {
    beforeEach(() => {
        cy.entrarComo(CONTAS.administrador);
    });

    it('navega pela sidebar até as listagens', () => {
        cy.contains('a', 'Dispositivos').click();
        cy.location('pathname').should('eq', '/administracao/dispositivos');
        cy.contains('Colostomia').should('be.visible');

        cy.contains('a', 'Orientações').click();
        cy.location('pathname').should('eq', '/administracao/orientacoes');

        cy.contains('a', 'Sinais de alerta').click();
        cy.location('pathname').should('eq', '/administracao/alertas');
        cy.contains('Sangramento').should('be.visible');
    });

    it('cadastra e exclui um sinal de alerta', () => {
        const nome = `Sinal de teste ${Date.now()}`;

        cy.visit('/administracao/alertas');
        cy.contains('a', 'Novo sinal').click();

        cy.get('#nome').type(nome);
        cy.get('#orientacao').type('Procure a equipe se o sintoma persistir.');
        cy.get('#gravidade').select('media');
        cy.contains('button', 'Salvar').click();

        cy.contains('Sinal de alerta cadastrado.').should('be.visible');
        cy.contains(nome).should('be.visible');

        // Exclusão passa por confirmação explícita.
        cy.contains('tr', nome).find(`[aria-label="Excluir ${nome}"]`).click();
        cy.contains('Confirmar exclusão').should('be.visible');
        cy.contains('button', 'Excluir').click();

        cy.contains('Sinal de alerta excluído.').should('be.visible');
        cy.contains(nome).should('not.exist');
    });

    it('conteúdo de texto exige o corpo', () => {
        cy.visit('/administracao/conteudos/create');

        cy.get('#titulo').type('Conteúdo de teste');
        cy.contains('button', 'Salvar').click();

        cy.contains('Escreva o conteúdo do texto.').should('be.visible');
    });

    it('trocar o tipo para vídeo troca o campo e passa a exigir a URL', () => {
        cy.visit('/administracao/conteudos/create');

        cy.get('#tipo').select('video');
        cy.get('#url_do_video').should('be.visible');
        cy.get('#corpo').should('not.exist');

        cy.get('#titulo').type('Vídeo de teste');
        cy.contains('button', 'Salvar').click();

        cy.location('pathname').should('eq', '/administracao/conteudos/create');
        cy.contains('Informe o endereço do vídeo.').should('be.visible');
    });

    it('cadastra um conteúdo de vídeo válido', () => {
        const titulo = `Vídeo de teste ${Date.now()}`;

        cy.visit('/administracao/conteudos/create');

        cy.get('#titulo').type(titulo);
        cy.get('#tipo').select('video');
        cy.get('#url_do_video').type('https://exemplo.com/video');
        cy.contains('button', 'Salvar').click();

        cy.contains('Conteúdo cadastrado.').should('be.visible');
        cy.contains(titulo).should('be.visible');
    });

    it('não deixa excluir dispositivo com paciente vinculado', () => {
        cy.visit('/administracao/dispositivos');

        cy.contains('tr', 'Colostomia')
            .find('[aria-label="Excluir Colostomia"]')
            .click();
        cy.contains('button', 'Excluir').click();

        cy.contains('paciente(s) vinculado(s)').should('be.visible');
        cy.contains('Colostomia').should('be.visible');
    });

    it('paciente não alcança a área administrativa', () => {
        cy.sair();
        cy.entrarComo(CONTAS.paciente);

        cy.request({
            url: '/administracao/dispositivos',
            failOnStatusCode: false,
        })
            .its('status')
            .should('eq', 403);
    });
});
