describe('Plugin setup of Author and Section Search Filters Plugin', function () {
    it('Enables Author and Section Search Filters plugin', function () {
		cy.login('dbarnes', null, 'publicknowledge');

		cy.contains('a', 'Website').click();

		cy.get('#plugins-button').click();

		cy.get('input[id^=select-cell-authorandsectionsearchfiltersplugin]').check();
		cy.get('input[id^=select-cell-authorandsectionsearchfiltersplugin]').should('be.checked');
    });
});