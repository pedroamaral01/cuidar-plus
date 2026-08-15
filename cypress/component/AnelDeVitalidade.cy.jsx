import AnelDeVitalidade from '@/Components/UI/AnelDeVitalidade';

/**
 * O anel traduz o percentual da semana em geometria de SVG. É um componente
 * com cálculo próprio e com um texto alternativo que precisa fazer sentido
 * para quem usa leitor de tela — dois motivos para testá-lo isolado.
 */
describe('AnelDeVitalidade', () => {
    it('descreve o percentual para leitores de tela', () => {
        cy.montar(<AnelDeVitalidade percentual={80} />);

        cy.get('[role="img"]').should(
            'have.attr',
            'aria-label',
            '80% dos cuidados da semana realizados',
        );
        cy.contains('80%').should('be.visible');
    });

    it('em 0% o arco de progresso fica totalmente recolhido', () => {
        cy.montar(<AnelDeVitalidade percentual={0} />);

        cy.get('circle')
            .last()
            .then(($arco) => {
                const circunferencia = Number($arco.attr('stroke-dasharray'));
                const deslocamento = Number($arco.attr('stroke-dashoffset'));

                // Sem progresso, o traço está deslocado por inteiro.
                expect(deslocamento).to.be.closeTo(circunferencia, 0.01);
            });
    });

    it('em 100% o arco fecha a volta completa', () => {
        cy.montar(<AnelDeVitalidade percentual={100} />);

        cy.get('circle')
            .last()
            .should('have.attr', 'stroke-dashoffset')
            .then((deslocamento) => {
                expect(Number(deslocamento)).to.be.closeTo(0, 0.01);
            });
    });

    it('em 50% o arco cobre metade da circunferência', () => {
        cy.montar(<AnelDeVitalidade percentual={50} />);

        cy.get('circle')
            .last()
            .then(($arco) => {
                const circunferencia = Number($arco.attr('stroke-dasharray'));
                const deslocamento = Number($arco.attr('stroke-dashoffset'));

                expect(deslocamento).to.be.closeTo(circunferencia / 2, 0.01);
            });
    });
});
