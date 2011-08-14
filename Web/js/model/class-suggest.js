/**
 * @file
 * Oversee class suggestion
 *
 * This is the first module to be written using JavaScript's class implementation
 */
window.ClassSuggest = function(formName) {
	var form = $(formName);
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
			url: '/college-class-enroll',
			type: 'post',
			data: form.serialize(),
			success: function(response) {
				content = '';

				if (response.error) {
					content = '<h2 class="error">' + response.message + '</h2>';
					dialog.open('enroll', content);

					$('.dialog-close', $P).live('click', function(e) {
						e.preventDefault();
						dialog.close()
					});
				}
				if (response.section_id) {
					content += '<h2>' + response.message + '</h2>' +
						'<hr />' +
						'<div class="suggested-reading">' +
							'<div id="enroll-book-list" class="book-list">' + 
							'</div>' +
						'</div>';

					dialog.open('enroll', content);

					$('.dialog-close', $P).live('click', function(e) {
						e.preventDefault();
						window.location = response.redirect;
						dialog.close()
					});

					if (!response.has_syllabus) {
						$('.suggested-reading').after('<hr />');
						doc.createForm('.dialog-inner', 'It seems no one has uploaded a syllabus for this class yet. Would you care to help us out?');
					}

					bookList = new BookSuggest('#enroll-book-list');
					bookList.getBookList(response.section_id);
				}
			}
		});
	}

}
