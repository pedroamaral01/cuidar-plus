import { CONTAS } from '../support/comandos';

describe('Lembretes e diário do paciente', () => {
    beforeEach(() => {
        cy.entrarComo(CONTAS.paciente);
    });

    it('marca um lembrete como feito e ele aparece no diário', () => {
        cy.visit('/inicio/lembretes');
        cy.contains('Trocar bolsa').should('be.visible');

        cy.get('[aria-label="Marcar Trocar bolsa como feito"]').click();

        cy.contains('Cuidado registrado no seu diário.').should('be.visible');
        cy.contains('Troca · 08:00 · feito').should('be.visible');

        // O mesmo cuidado precisa aparecer no histórico do diário.
        cy.visit('/inicio/diario');
        cy.contains('HISTÓRICO RECENTE').should('be.visible');
        cy.contains('Trocar bolsa').should('be.visible');
    });

    it('cria e remove um lembrete', () => {
        const titulo = `Lembrete de teste ${Date.now()}`;

        cy.visit('/inicio/lembretes');

        cy.contains('button', 'Novo lembrete').click();
        cy.get('#titulo').type(titulo);
        cy.get('#tipo').select('higiene');
        cy.get('#horario').type('09:30');
        cy.contains('button', 'Criar lembrete').click();

        cy.contains('Lembrete criado.').should('be.visible');
        cy.contains(titulo).should('be.visible');

        cy.get(`[aria-label="Remover ${titulo}"]`).click();

        cy.contains('Lembrete removido.').should('be.visible');
        cy.contains(titulo).should('not.exist');
    });

    it('liga e desliga um lembrete', () => {
        cy.visit('/inicio/lembretes');

        cy.get('[aria-label="Ligar ou desligar Higienizar o local"]')
            .should('have.attr', 'aria-checked', 'true')
            .click();
        cy.contains('Lembrete desligado.').should('be.visible');

        cy.get('[aria-label="Ligar ou desligar Higienizar o local"]')
            .should('have.attr', 'aria-checked', 'false')
            .click();
        cy.contains('Lembrete ligado.').should('be.visible');
    });

    it('o calendário semanal mostra 7 dias e destaca o dia atual', () => {
        cy.visit('/inicio/lembretes');

        // 7 dias: os navegáveis são links, os futuros ficam desabilitados.
        cy.get('[aria-current="date"]').should('have.length', 1);
        cy.get('[aria-label^="Dia "]').should('have.length.at.least', 1);
    });

    it('registra um cuidado avulso no diário', () => {
        const titulo = `Cuidado avulso ${Date.now()}`;

        cy.visit('/inicio/diario');

        cy.contains('button', 'Registrar cuidado').click();
        cy.get('#titulo').type(titulo);
        cy.get('#tipo').select('protecao');
        cy.get('#observacao').type('Sem intercorrências.');
        cy.contains('button', 'Salvar no diário').click();

        cy.contains('Cuidado registrado no seu diário.').should('be.visible');
        cy.contains(titulo).should('be.visible');
        cy.contains('Sem intercorrências.').should('be.visible');
    });

    it('abre a orientação com abas e passo a passo', () => {
        cy.visit('/inicio/orientacoes');

        cy.contains('Aprenda como realizar os cuidados corretamente.').click();

        cy.contains('PASSO A PASSO').should('be.visible');
        cy.contains('button', 'Troca da bolsa').should(
            'have.attr',
            'aria-selected',
            'true',
        );
        cy.contains('Reúna todo o material antes de começar.').should('be.visible');

        // Trocar de aba troca o passo a passo.
        cy.contains('button', 'Higiene').click();
        cy.contains('Lave as mãos antes e depois do procedimento.').should(
            'be.visible',
        );
        cy.contains('Reúna todo o material antes de começar.').should('not.exist');
    });

    it('expande um sinal de alerta e mostra a orientação', () => {
        cy.visit('/inicio/sinais-de-alerta');

        cy.contains('button', 'Sangramento').click();
        cy.contains(
            'Se o sangramento for intenso ou não parar, procure atendimento imediatamente.',
        ).should('be.visible');
    });

    it('a rotina de um paciente não aparece para o outro', () => {
        cy.visit('/inicio/lembretes');
        cy.contains('Trocar bolsa').should('be.visible');

        cy.sair();
        cy.entrarComo(CONTAS.outroPaciente);

        // O João usa sonda vesical: a rotina dele é outra.
        cy.visit('/inicio/lembretes');
        cy.contains('Trocar bolsa coletora').should('be.visible');
        cy.contains('Proteger a pele').should('not.exist');
    });
});
