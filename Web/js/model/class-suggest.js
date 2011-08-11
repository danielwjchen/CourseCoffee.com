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
				content = '';
				if (response.redirect) {
					window.location = response.redirect;
				}

				if (response.error) {
					content = '<h2 class="error">' + response.message + '</h2>';
					dialog.open('enroll', content);
				}
				if (response.section_id) {
					content += '<h2>' + response.message + '</h2>' +
						'<div class="suggested-reading">' +
							'<h3>Suggested Reading</h3>' +
							'<div id="enroll-book-list" class="book-list"></div>' +
						'</div>';

					dialog.open('enroll', content);

					bookList = new BookSuggest('#enroll-book-list');
					bookList.getBookList(response.section_id);
				}
			}
		});
	}

}
