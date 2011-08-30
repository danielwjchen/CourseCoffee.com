/**
 * @file
 * Manage user events and their corresponding javascript actions on book-search 
 * page
 */
$P.ready(function() {

	var bookSearch = $('.book-search');

	var bookSearchSuggest = new BookSearchSuggest('#book-suggest-form', '#suggest-input');

	bookSearch.delegate('a.button', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('suggest')) {
			e.preventDefault();
			bookSearchSuggest.submit();

		}
	});
});
