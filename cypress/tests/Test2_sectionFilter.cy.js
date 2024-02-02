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
    it('Search submissions using the section AND author filter', function () {
        cy.visit('publicknowledge/search');
        cy.get('#sections').select(expectedSections[0]);
        cy.get('#authors').select("Alan Mwandenga");

        cy.contains('button', 'Search').click();
        cy.contains("The Signalling Theory Dividends");
    });
    //  TO DO:
    
    // it('Search submissions using the section filter', function () {
    //     cy.visit('publicknowledge/search');
    //     cy.get('#sections').select(expectedSections[0]);
    //     cy.contains('button', 'Search').click();
    //     cy.contains("Antimicrobial, heavy metal resistance and plasmid profile of coliforms isolated from nosocomial infections in a hospital in Isfahan, Iran");
    //     cy.contains("The Signalling Theory Dividends");

    //     cy.get('#sections').select(expectedSections[1]);
    //     cy.contains('button', 'Search').click();
    //     cy.contains('No Results');
    // });
    // it('Keep the value of the filter after search', function () {
    //     cy.visit('publicknowledge/search');
    //     cy.get('#sections').select(expectedSections[0]);
    //     cy.contains('button', 'Search').click();
    //     cy.get('#sections').should('have.value', expectedSections[0]);
    // });
});