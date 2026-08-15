import { CONTAS } from '../support/comandos';

describe('Conteúdos, perfil e falar com a equipe', () => {
    beforeEach(() => {
        cy.entrarComo(CONTAS.paciente);
    });

    it('favorita um conteúdo e ele aparece no filtro de favoritos', () => {
        cy.visit('/inicio/conteudos');

        const titulo = 'Alimentação após a alta hospitalar';

        cy.get(`[aria-label="Favoritar ${titulo}"]`).click();
        cy.contains('Conteúdo salvo nos favoritos.').should('be.visible');

        cy.contains('button', 'Favoritos').click();
        cy.contains(titulo).should('be.visible');

        // Desfavoritar tira do filtro.
        cy.get(`[aria-label="Remover ${titulo} dos favoritos"]`).click();
        cy.contains('Conteúdo removido dos favoritos.').should('be.visible');

        cy.contains('button', 'Favoritos').click();
        cy.contains(titulo).should('not.exist');
    });

    it('o filtro de vídeos mostra só os vídeos', () => {
        cy.visit('/inicio/conteudos');

        cy.contains('button', 'Vídeos').click();

        cy.contains('Vídeo: troca da bolsa passo a passo').should('be.visible');
        cy.contains('Alimentação após a alta hospitalar').should('not.exist');
    });

    it('abre um conteúdo de texto e lê o corpo', () => {
        cy.visit('/inicio/conteudos');

        cy.contains('Como identificar sinais de infecção').click();

        cy.contains('A pele ao redor do dispositivo deve estar íntegra').should(
            'be.visible',
        );
        cy.contains('não substitui a orientação da equipe').should('be.visible');
    });

    it('atualiza os dados do perfil', () => {
        cy.visit('/inicio/perfil');

        cy.get('#telefone').clear().type('(31) 91234-5678');
        cy.get('#cuidador_nome').clear().type('Joana de Teste');
        cy.contains('button', 'Salvar alterações').click();

        cy.contains('Perfil atualizado.').should('be.visible');

        cy.reload();
        cy.get('#cuidador_nome').should('have.value', 'Joana de Teste');
    });

    it('o perfil mostra o dispositivo atual do paciente', () => {
        cy.visit('/inicio/perfil');

        cy.contains('DISPOSITIVO ATUAL').should('be.visible');
        cy.contains('Colostomia').should('be.visible');
    });

    it('falar com a equipe mostra apenas o ponto de entrada', () => {
        cy.visit('/inicio/falar-com-a-equipe');

        cy.contains('Em breve').should('be.visible');
        cy.contains('ainda está sendo preparado').should('be.visible');

        // Não existe campo de envio: o canal ainda não foi decidido.
        cy.get('textarea').should('not.exist');
        cy.get('input[type="text"]').should('not.exist');
    });
});
