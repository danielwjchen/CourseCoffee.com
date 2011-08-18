/**
 * @file
 * Suggest a list of class base on input string and enroll user to the best match
 *
 * This is a child class of ClassSuggest
 * @see js/model/class-suggest.js
 */
window.ClassEnroll = function(formName, inputName) {

	var form = $(formName);
	blurInput(formName);

	/**
	 * Submit the suggested class and enroll user
	 */
	var submitEnroll = function(section_id) {
		if (section_id != undefined) {
			$('#section-id', form).val(section_id);
			$.ajax({
				url: '/college-class-enroll',
				type: 'post',
				data: form.serialize(),
				success: function(response) {
					var content = '';

					if (response.error) {
						content = '<h2 class="error">' + response.message + '</h2>';
						dialog.open('enroll', content);

						$('.dialog-close', $P).live('click', function(e) {
							e.preventDefault();
							dialog.close()
						});
					}
					if (response.message) {
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
	};

	var classSuggest = new ClassSuggest(formName, inputName, submitEnroll);

	/**
	 * Enroll the user to class
	 */
	this.enroll = function() {
		submitEnroll();
	}

};
