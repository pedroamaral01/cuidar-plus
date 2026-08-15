import { CONTAS } from '../support/comandos';

const CELULAR = { largura: 390, altura: 844 };
const COMPUTADOR = { largura: 1280, altura: 800 };

describe('Navegação responsiva', () => {
    describe('no computador', () => {
        beforeEach(() => {
            cy.viewport(COMPUTADOR.largura, COMPUTADOR.altura);
            cy.entrarComo(CONTAS.paciente);
        });

        it('mostra a barra lateral com todos os itens', () => {
            cy.visit('/inicio');

            cy.contains('a', 'Meu dispositivo').should('be.visible');
            cy.contains('a', 'Orientações').should('be.visible');
            cy.contains('a', 'Sinais de alerta').should('be.visible');
            cy.contains('a', 'Conteúdos').should('be.visible');
            cy.contains('a', 'Falar com a equipe').should('be.visible');
            cy.contains('a', 'Meu perfil').should('be.visible');

            // No computador não existe painel "Mais": tudo cabe na lateral.
            cy.contains('button', 'Mais').should('not.be.visible');
        });

        it('destaca a seção atual na barra lateral', () => {
            cy.visit('/inicio/lembretes');

            cy.get('[aria-current="page"]').should('contain', 'Lembretes');
        });
    });

    describe('no celular', () => {
        beforeEach(() => {
            cy.viewport(CELULAR.largura, CELULAR.altura);
            cy.entrarComo(CONTAS.paciente);
        });

        it('mostra a barra inferior com os 4 itens fixos mais o botão "Mais"', () => {
            cy.visit('/inicio');

            // A sidebar existe no DOM mas está oculta no celular; por isso a
            // busca é feita dentro da navegação visível.
            cy.get('nav:visible').within(() => {
                cy.contains('a', 'Início').should('be.visible');
                cy.contains('a', 'Lembretes').should('be.visible');
                cy.contains('a', 'Diário').should('be.visible');
                cy.contains('a', 'Notificações').should('be.visible');
                cy.contains('button', 'Mais').should('be.visible');

                cy.contains('a', 'Falar com a equipe').should('not.exist');
            });
        });

        it('o painel "Mais" reúne as seções que não cabem na barra inferior', () => {
            cy.visit('/inicio');

            cy.get('nav:visible').contains('button', 'Mais').click();

            cy.get('[role="dialog"]').within(() => {
                cy.contains('a', 'Meu dispositivo').should('be.visible');
                cy.contains('a', 'Orientações').should('be.visible');
                cy.contains('a', 'Sinais de alerta').should('be.visible');
                cy.contains('a', 'Conteúdos').should('be.visible');
                cy.contains('a', 'Falar com a equipe').should('be.visible');
                cy.contains('a', 'Meu perfil').should('be.visible');
                cy.contains('button', 'Sair da conta').should('be.visible');
            });
        });

        it('navegar pelo painel "Mais" leva à tela e fecha o painel', () => {
            cy.visit('/inicio');

            cy.get('nav:visible').contains('button', 'Mais').click();
            cy.get('[role="dialog"]').contains('a', 'Sinais de alerta').click();

            cy.location('pathname').should('eq', '/inicio/sinais-de-alerta');
            // O painel precisa fechar, senão fica sobreposto à tela nova.
            cy.get('[role="dialog"]').should('not.exist');
        });

        it('fecha o painel "Mais" pelo botão de fechar', () => {
            cy.visit('/inicio');

            cy.contains('button', 'Mais').click();
            cy.get('[aria-label="Fechar"]').last().click();

            cy.contains('button', 'Sair da conta').should('not.exist');
        });

        it('o cabeçalho do celular leva às notificações pelo sino', () => {
            cy.visit('/inicio');

            cy.get('header a[aria-label^="Notificações"]').click();

            cy.location('pathname').should('eq', '/inicio/notificacoes');
        });

        it('a página não rola para os lados', () => {
            cy.visit('/inicio/diario');

            cy.document().then((documento) => {
                const largura = documento.documentElement;

                expect(
                    largura.scrollWidth,
                    'a largura do conteúdo não pode passar da tela',
                ).to.be.at.most(largura.clientWidth + 1);
            });
        });

        it('as tabelas da administração rolam dentro do próprio quadro', () => {
            // Sem cy.sair(): o botão de sair fica na barra lateral, oculta no
            // celular. O entrarComo já limpa a sessão anterior.
            cy.entrarComo(CONTAS.administrador);
            cy.visit('/administracao/pacientes');

            cy.document().then((documento) => {
                const raiz = documento.documentElement;

                expect(raiz.scrollWidth).to.be.at.most(raiz.clientWidth + 1);
            });

            cy.get('table').parent().should('have.css', 'overflow-x', 'auto');
        });
    });
});
