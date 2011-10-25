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
						content = ClassEnroll.handleError(response);
						dialog.open('enroll', content);

						$('.dialog-close', $P).live('click', function(e) {
							e.preventDefault();
							dialog.close()
						});

						var dialogRegion = $('.dialog');
						$('.button.cancel-action').click(function(e) {
							e.preventDefault();
							dialog.close();
						});
						$('.button.go-to-class-page').click(function(e) {
							e.preventDefault();
							window.location = response.redirect;
						});
						ClassRemove.removeClassFromList(dialogRegion, function(section_id) {
							var confirmAction = $('.button.confirm-action', dialogRegion);
							confirmAction.removeClass('disabled');
							confirmAction.click(function(e) {
								e.preventDefault();
								$.ajax({
									url: '/college-class-enroll',
									type: 'post',
									async: false,
									data: form.serialize()
								});
								window.location = response.redirect;
							});
						});


					} else {
						content += '<h2>' + response.message + '</h2>' +
							'<hr />' +
							'<div class="suggested-reading">' +
								'<div id="enroll-book-list" class="book-list">' + 
								'</div>' +
							'</div>';

						dialog.open('enroll', content);

						if (!response.has_syllabus) {
							$('.suggested-reading').after('<hr />');
							doc.createForm('.dialog-inner', 'It seems no one has uploaded a syllabus for this class yet. Would you care to help us out?');
							$('#doc-upload-form input[name=section-id]').val(response.section_id);
							$('#doc-upload-form').after('<span class="double-underline"><a class="cancel" href="#">no, thanks</a></span>');

						} else {
							$('#enroll-book-list').height('350');
							$('.dialog-inner').append('<a class="button redirect" href="#">go to class page</a>');
						}

						var bookList = new BookSuggest('#enroll-book-list');
						bookList.getBookList(response.section_id);

						$('a', $P).live('click', function(e) {
							if ($(this).hasClass('dialog-close') || $(this).hasClass('cancel') || $(this).hasClass('redirect')) {
								e.preventDefault();
								window.location = response.redirect;
								dialog.close()
							}
						});
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

/**
 * Handle errors encountered during enrollment
 *
 * @param object response
 *
 * @return string content
 */
ClassEnroll.handleError = function(response) {
	var content = '<div class="dialog-content dialog-remove-class"><h2>' + response.message + '</h2>';
	switch (response.error) {
		case 'already_enrolled':
			content += '<a href="#" class="go-to-class-page button">go to class page</a>';
			break;
		case 'exceed_max':
			content += "<p>Well, this is awkward. You will have to remove a class to make room for the new one. Remember, you can always add it back, and the assignment you've created won't be lost.</p>" +
			ClassRemove.listClassToRemove(response.class_list) +
			'<div class="options">' +
				'<a href="#" class="button disabled confirm-action">confirm</a>' +
				'<a href="#" class="button cancel-action">cancel</a>' +
			'</div>';
			break;
		default:
			break;
	}
	return content + '</div>';
};
