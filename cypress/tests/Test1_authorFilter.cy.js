describe('The advanced search filters for Authors', function () {
    it('Should be a dropdown list of authors', function () {
        cy.visit('');
        cy.contains('Search').click();
        cy.get('#authors').should('be.visible').and('have.prop', 'tagName', 'SELECT');
    });
});