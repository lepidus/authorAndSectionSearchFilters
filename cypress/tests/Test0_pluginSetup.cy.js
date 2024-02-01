describe('Plugin setup of Custom Search Filters Plugin', function () {
    it('Enables Custom Search Filters plugin', function () {
		cy.login('dbarnes', null, 'publicknowledge');

		cy.contains('a', 'Website').click();

		cy.waitJQuery();
		cy.get('#plugins-button').click();

		cy.get('input[id^=select-cell-customsearchfiltersplugin]').check();
		cy.get('input[id^=select-cell-customsearchfiltersplugin]').should('be.checked');
    });
});