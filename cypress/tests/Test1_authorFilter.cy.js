describe('Author search filter replacement', function () {
    const expectedAuthorsCount = 4;
    const expectedAuthors = ["Vajiheh Karbasizaed", "Alan Mwandenga"];

    it('Field should be a dropdown list of authors', function () {
        cy.visit('publicknowledge/search');
        cy.get('#authors').should('be.visible').and('have.prop', 'tagName', 'SELECT');
        cy.get('#authors').should('have.value', '');
        cy.get('#authors').children().should('have.length', expectedAuthorsCount + 1);
        cy.contains('#authors option', expectedAuthors[0]);
        cy.contains('#authors option', expectedAuthors[1]);
    });
    it('Search submissions using the author filter', function () {
        cy.visit('publicknowledge/search');

        cy.get('#authors').select(expectedAuthors[0]);
        cy.contains('button', 'Search').click();
        cy.contains("Antimicrobial, heavy metal resistance and plasmid profile of coliforms isolated from nosocomial infections in a hospital in Isfahan, Iran");
        
        cy.get('#authors').select(expectedAuthors[1]);
        cy.contains('button', 'Search').click();
        cy.contains("The Signalling Theory Dividends");
    });
    it('Keep the value of the filter after search', function () {
        cy.visit('publicknowledge/search');
        cy.get('#authors').select(expectedAuthors[0]);
        cy.contains('button', 'Search').click();
        cy.get('#authors').should('have.value', expectedAuthors[0]);
    });
});