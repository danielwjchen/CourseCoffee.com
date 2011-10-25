/**
 * @file
 * Manage user events and their corresponding javascript actions on item-search 
 * page
 */
$P.ready(function() {

	var itemSearch = $('.item-search');

	var itemSearchSuggest = new BookSearchSuggest('#item-suggest-form', '#suggest-input');

	var default_section = $('#item-suggest-form input[name=section_id]').val();
	if (default_section != '') {
		itemSearchSuggest.submit();
	}

	itemSearch.delegate('a', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('suggest')) {
			e.preventDefault();
			itemSearchSuggest.submit();

		} else if (target.hasClass('upload')) {
			e.preventDefault();
			doc.init();
		}

	});
});
