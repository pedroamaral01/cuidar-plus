describe('Cadastro do paciente em 3 passos', () => {
    it('percorre os 3 passos e cria a conta já autenticada', () => {
        cy.emailDeTeste().then((email) => {
            cy.visit('/cadastro');

            // ---- Passo 1: dados pessoais ----
            cy.contains('Dados pessoais').should('be.visible');
            cy.get('#nome').type('Maria de Teste');
            cy.get('#email').type(email);
            cy.get('#password').type('senha1234');
            cy.get('#password_confirmation').type('senha1234');
            cy.get('#telefone').type('(31) 99999-0000');

            cy.contains('Tem um cuidador ou familiar?').click();
            cy.get('#cuidador_nome').type('Joana de Teste');

            cy.contains('button', 'Continuar').click();

            // ---- Passo 2: dispositivo ----
            cy.contains('Selecione o dispositivo que você utiliza').should('be.visible');
            // O botão de continuar fica bloqueado até escolher o dispositivo.
            cy.contains('button', 'Continuar').should('be.disabled');

            cy.contains('button', 'Colostomia').click();
            cy.contains('button', 'Continuar').should('not.be.disabled').click();

            // ---- Passo 3: plano de cuidados ----
            cy.contains('Revise o plano de cuidados').should('be.visible');
            cy.contains('Colostomia').should('be.visible');
            cy.contains('Trocar bolsa').should('be.visible');

            cy.contains('button', 'Concluir cadastro').click();

            // Cadastro conclui autenticado, direto no Início.
            cy.location('pathname').should('eq', '/inicio');
            cy.contains('Olá, Maria').should('be.visible');
        });
    });

    it('volta ao passo 1 quando o e-mail já está em uso', () => {
        cy.visit('/cadastro');

        cy.get('#nome').type('Maria Repetida');
        cy.get('#email').type('maria@cuidarplus.local');
        cy.get('#password').type('senha1234');
        cy.get('#password_confirmation').type('senha1234');
        cy.contains('button', 'Continuar').click();

        cy.contains('button', 'Colostomia').click();
        cy.contains('button', 'Continuar').click();
        cy.contains('button', 'Concluir cadastro').click();

        // O erro é do passo 1, então o formulário precisa voltar para lá.
        cy.contains('Já existe uma conta com esse e-mail.').should('be.visible');
        cy.get('#nome').should('be.visible');
    });

    it('a confirmação de senha divergente impede o cadastro', () => {
        cy.emailDeTeste().then((email) => {
            cy.visit('/cadastro');

            cy.get('#nome').type('Maria de Teste');
            cy.get('#email').type(email);
            cy.get('#password').type('senha1234');
            cy.get('#password_confirmation').type('outra-senha');
            cy.contains('button', 'Continuar').click();

            cy.contains('button', 'Colostomia').click();
            cy.contains('button', 'Continuar').click();
            cy.contains('button', 'Concluir cadastro').click();

            cy.contains('A confirmação da senha não confere.').should('be.visible');
            cy.location('pathname').should('eq', '/cadastro');
        });
    });
});
