/**
 * @file
 * Oversee class suggestion
 *
 * This is the first module to be written using JavaScript's class implementation
 */
window.ClassSuggest = function(formName) {
	form = $(formName);
	/**
	 * Get suggestions
	 */
	this.suggest = function(string) {
	}

	/**
	 * Enroll the user to class
	 */
	this.enroll = function() {
		$.ajax({
			url: 'college-class-enroll',
			type: 'post',
			data: form.serialize(),
			success: function(response) {
				// get list of suggested reading on success
				if (response.section_id) {
					content = response.message + 
						'<div class="suggested-reading">' +
							'<h3>Suggested Reading</h3>' +
							'<div id="book-list"></div>' +
						'</div>';
					dialog.open('enroll', content);
					bookList = new BookSuggest('#book-list');
					bookList.getBookList(response.section_id);
				}
				if (response.redirect) {
					window.location = response.redirect;
				}
			}
		});
	}

}
