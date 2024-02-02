describe('Custom Search Filters - Authors filter replacement', function () {
    const expectedSectionsCount = 2;
    const expectedSections = ["Articles", "Reviews"];

    it('New field should be a dropdown list of sections', function () {
        cy.visit('publicknowledge/search');
        cy.get('#sections').should('be.visible').and('have.prop', 'tagName', 'SELECT');
        cy.get('#sections').should('have.value', '');
        cy.get('#sections').children().should('have.length', expectedSectionsCount + 1);
        cy.contains('#sections option', expectedSections[0]);
        cy.contains('#sections option', expectedSections[1]);
    });
});