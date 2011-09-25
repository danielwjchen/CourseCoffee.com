/**
 * @file
 * Manage user events and their corresponding javascript actions on book-search 
 * page
 */
$P.ready(function() {

	var bookSearch = $('.book-search');

	var bookSearchSuggest = new BookSearchSuggest('#book-suggest-form', '#suggest-input');

	var default_section = $('#book-suggest-form input[name=section_id]').val();
	if (default_section != '') {
		bookSearchSuggest.submit();
	}

	bookSearch.delegate('a', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('suggest')) {
			e.preventDefault();
			bookSearchSuggest.submit();

		} else if (target.hasClass('upload')) {
			e.preventDefault();
			doc.init();
		}

	});
});
