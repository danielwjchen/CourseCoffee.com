/**
 * @file
 *
 * Extend Autocomplete to provide class reading seach function on book search 
 * page.
 */
BookSearchSuggest = function(formName, inputName) {

	var _form = $(formName);
	blurInput(formName);

	/**
	 * A callback function to get the book list ob submit
	 */
	var _getBookSuggest = function(section_id) {
		if (section_id != undefined) {
			$('.content').removeClass('hidden');
			var bookList = new BookSuggest('#book-suggest-list');
			bookList.getBookList(section_id);
		}
		$(document.getElementById('hint-message')).addClass('hidden');
	}

	var _classSuggest = new ClassSuggest(formName, inputName, _getBookSuggest);

	/**
	 * submit the form
	 */
	this.submit = function() {
		var section_id = $('input[name=section_id]', _form).val()
		if (section_id != '') {
			_getBookSuggest(section_id);
		}
	}

};
