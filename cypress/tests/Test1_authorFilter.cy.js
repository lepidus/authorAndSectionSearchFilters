describe('Author search filter replacement', function () {
    const expectedAuthorsCount = 4;
    const expectedAuthors = ["Vajiheh Karbasizaed", "Amina Mansour", "Alan Mwandenga", "Nicolas Riouf"];
    const expectedAuthorsNamesInSelection = ["Karbasizaed, Vajiheh", "Mansour, Amina", "Mwandenga, Alan", "Riouf, Nicolas"];

    it('Field should be a dropdown list of authors', function () {
        cy.visit('publicknowledge/search');
        cy.get('#authors').should('be.visible').and('have.prop', 'tagName', 'SELECT');

        cy.get('#authors').should('have.value', '');
        cy.get('#authors').children().eq(0).should('have.text', 'None selected');

        cy.get('#authors').children().should('have.length', expectedAuthorsCount + 1);
        cy.contains('#authors option', expectedAuthorsNamesInSelection[0]);
        cy.contains('#authors option', expectedAuthorsNamesInSelection[1]);
    });
    it('Search submissions using the author filter', function () {
        cy.visit('publicknowledge/search');

        cy.get('#authors').select(expectedAuthorsNamesInSelection[0]);
        cy.contains('button', 'Search').click();
        cy.contains("Antimicrobial, heavy metal resistance and plasmid profile of coliforms isolated from nosocomial infections in a hospital in Isfahan, Iran");

        cy.get('#authors').select(expectedAuthors[1]);
        cy.contains('button', 'Search').click();
        cy.contains("The Signalling Theory Dividends");
    });
    it('Keep the value of the filter after search', function () {
        cy.visit('publicknowledge/search');
        cy.get('#authors').select(expectedAuthorsNamesInSelection[0]);
        cy.contains('button', 'Search').click();
        cy.get('#authors').should('have.value', expectedAuthors[0]);
    });
    it('The authors list should be ordered alphbetically by last name', function () {
        cy.visit('publicknowledge/search');
        cy.get('#authors').children().eq(1).should('have.text', expectedAuthorsNamesInSelection[0]);
        cy.get('#authors').children().eq(2).should('have.text', expectedAuthorsNamesInSelection[1]);
        cy.get('#authors').children().eq(3).should('have.text', expectedAuthorsNamesInSelection[2]);
    });
});