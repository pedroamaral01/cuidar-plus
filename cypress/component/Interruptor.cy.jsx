import Interruptor from '@/Components/UI/Interruptor';
import { useState } from 'react';

/**
 * O interruptor é o controle que liga e desliga um lembrete. Aqui ele é
 * testado isolado, porque o que importa é o contrato de acessibilidade
 * (role=switch + aria-checked) e o comportamento de alternância — coisas
 * difíceis de afirmar com precisão no meio de uma tela inteira.
 */
function InterruptorControlado({ inicial = false, ...props }) {
    const [ativo, setAtivo] = useState(inicial);

    return (
        <Interruptor
            ativo={ativo}
            aoAlternar={() => setAtivo(!ativo)}
            rotulo="Ligar ou desligar Trocar bolsa"
            {...props}
        />
    );
}

describe('Interruptor', () => {
    it('expõe role de switch e o estado para leitores de tela', () => {
        cy.montar(<InterruptorControlado inicial />);

        cy.get('[role="switch"]')
            .should('have.attr', 'aria-checked', 'true')
            .and('have.attr', 'aria-label', 'Ligar ou desligar Trocar bolsa');
    });

    it('alterna o estado ao ser clicado', () => {
        cy.montar(<InterruptorControlado inicial={false} />);

        cy.get('[role="switch"]').should('have.attr', 'aria-checked', 'false');

        cy.get('[role="switch"]').click();
        cy.get('[role="switch"]').should('have.attr', 'aria-checked', 'true');

        cy.get('[role="switch"]').click();
        cy.get('[role="switch"]').should('have.attr', 'aria-checked', 'false');
    });

    it('não alterna quando está desabilitado', () => {
        cy.montar(<InterruptorControlado inicial={false} disabled />);

        cy.get('[role="switch"]').should('be.disabled');
        cy.get('[role="switch"]').click({ force: true });
        cy.get('[role="switch"]').should('have.attr', 'aria-checked', 'false');
    });
});
