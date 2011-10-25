/**
 * @file
 * Interact with user when action is taken on /doc-editor
 */
window.EditorFlow = function(taskCreationFormClassName) {
	var _taskCreationForm   = $(taskCreationFormClassName);
	var _section_id_field   = $('input[name=section_id]', _taskCreationForm);
	var _section_code_field = $('input[name=section_code]', _taskCreationForm);
	var _section_id         = _section_id_field.val();
	var _section_code       = _section_code_field.val() ;
	var _redirect           = '';
	var _confirmationMessage = {
		'hasSectionId' : "<h1>Just to be sure.</h1><h2>Please confirm which class this document is for.</h2>",
		'hasNoSectionId' : '<h1 class="no-section-id">What class is this for?</h1><h2>Type something below, and we will guess.</h2>'
	}
	var _taskCreationMessage = '';
	var _bookList = new BookSuggest('.book-list');
	/**
	 * A helper function to redirect page
	 */
	var _goToClassPage = function(url) {
		window.location = url;
	};

	/**
	 * Enroll user to class
	 */
	var _enrollUserToClass = function() {
		$.ajax({
			url: '/college-class-enroll',
			type: 'post',
			data: 'section_id=' + _section_id,
			async: false,
			success: function(response) {

				if (response.error) {
					_taskCreationMessage += ClassEnroll.handleError(response);
				} else {
					_taskCreationMessage += '<h3>' + response.message + '</h3>';
					_taskCreationMessage += '<a href="#" class="go-to-class-page button">go to class page</a>';
				}

				_redirect = response.redirect ? response.redirect : '';
			}
		});
	};

	/**
	 * Suggest user to sign up
	 */
	var _suggestUserSignUp = function() {
		var signUpOption = SignUp.getOptions();
		var fbUid = '';
		$FB(function() {
			FB.getLoginStatus(function(response) {
				if (response.authResponse) {
					fbUid = response.authResponse.userID;
				}
			});
		});

		_taskCreationMessage += "<h2>In the meantime, why don't we create an account?</h2>" + SignUp.getOptions();
		$('.dialog-inner').delegate('a.sign-up', 'click', function(e) {
			e.preventDefault();
			var target = $(this);
			var url = target.attr('href') + '?section_id=' + _section_id;
			if (target.hasClass('facebook')) {
				window.location = url + '&fb=true&fb_uid=' + fbUid;
			} else {
				window.location = url;
			}
		});
	};

	/**
	 * Prompt user to confirm class information
	 *
	 * On confirmation, it notifies the system to start looking for book 
	 * information.
	 */
	this.promptClassConfirmation = function() {
		var question = '';
		var buttonStatus = ' disabled';
		if (_section_id == '') {
			question = 'hasNoSectionId';
			_section_code = 'e.g. AAC 101';
		} else {
			question = 'hasSectionId';
			buttonStatus = '';
		}
		var content = _confirmationMessage[question] +
		'<form id="class-confirmation-form">' +
			'<div class="row">' +
				'<label for="string">Class: </label>' +
				'<input type="hidden" name="section_id" value="' + _section_id + '" />' +
				'<input type="text" id="suggest-input" name="string" value="' + _section_code + '" />' +
			'</div>' +
			'<div class="confirm-row row">' +
				'<a href="#" class="button confirm-class-info' + buttonStatus + '">confirm</a>' +
			'</div>' +
		'</form>';
		dialog.open('confirm-class', content);
		blurInput('#class-confirmation-form');

		// get the editor page version of class-suggest
		var classEdit = new ClassEdit('#class-confirmation-form', '#suggest-input');

		$('.dialog-close').click(function(e) {
			e.preventDefault();
		});

		// notify server of the class and start caching the book info
		$('.confirm-class a.confirm-class-info').click(function(e) {
			e.preventDefault();
			var updated_section_id   = $('#class-confirmation-form input[name=section_id]').val();
			_section_id_field.val(updated_section_id);
			_section_id = updated_section_id;
			var updated_section_code = $('#class-confirmation-form input[name=string]').val();
			_section_code_field.val(updated_section_code);
			_section_code = updated_section_code;
			dialog.close()
			_bookList.getBookList(updated_section_id);
		});
	};

	/**
	 * Prompt user to confirm task creation
	 *
	 * This action has two different flow depend on where user comes from.
	 *  - a first-time user and goes on to register for an 
	 *    account
	 *  - the user already has an account, proceeds to the enroll in the class
	 *    or not, and then go to the class page
	 *
	 * @param array taskList
	 *  A list of schedule generated from document
	 */
	this.promptTaskCreation = function(taskList) {
		var content = '<div class="dialog-content">' +
			'<div class="confirm-message">' +
				"<h2>Before we submit everything, let's take a final look... </h2>" + 
				'<ul>' +
					'<li><span class="orange-checker">&#10004;</span> Are all the dates correct?</li>' +
					'<li><span class="orange-checker">&#10004;</span> Are there missing assignments?</li>' +
					'<li><span class="orange-checker">&#10004;</span> Do the assignments make sense?</li>' +
				'</ul>' +
				'<h3>If everything looks okay, hit the confirm button below at anytime.</h3>' +
			'</div>' +
			'<div class="row confirm-row">' +
				'<a href="#" class="button confirm-task-creation">confirm</a>' +
			'</div>' +
		'</div>';
		dialog.open('confirm-task', content);

		/**
		 * Confirm task creation
		 */
		$('.confirm-task-creation').live('click', function(e) {
			e.preventDefault();
			var confirmButton = $(this);
			$('.confirm-message').after('<h3>Please wait while our server monkeys process your request</h3>');
			confirmButton.addClass('disabled');

			var taskCreationForm = $('#task-creation-form');
			var processState = $('input[name=process_state]', taskCreationForm).val();
			var creationForm = $('#task-creation-form');
			creationForm.append('<input type="hidden" name="task_count" value="' + taskList.length + '" />');

			$('.dialog-close').live('click', function(e) {
				e.preventDefault();
				dialog.close()
			});

			/**
			 * Go throught the task list and append items to task creation form
			 */
      var cnt = 0; 
			var curr_date = null;
			var sch_content = null;
      for(var i = 0; i < taskList.length; i++){
				if(taskList[i].deleted == false){
					curr_date = taskList[i].date;
					sch_date = (curr_date.getMonth() + 1) + "/" + (curr_date.getDate()) + "/" + "2011";
					sch_content = taskList[i].content;
					creationForm.append('<input type="hidden" name="date_' + cnt + '" value="' + sch_date + '" />');
					creationForm.append('<input type="hidden" name="objective_' + cnt + '" value="' + sch_content + '" />');
					cnt = cnt + 1;
        }
      }

			$.ajax({
				url: '/task-add-from-doc',
				type: 'post',
				cache: false,
				data: creationForm.serialize(),
				success: function(response) {
					var bookList = $('.suggested-reading').clone();
					bookList.removeClass('hidden');
					_taskCreationMessage = '<h3 class="title">' + response.message + '</h3>';
					_taskCreationMessage += $('img', bookList).length ? '<hr />' + bookList.html() : '<hr />';

					/**
					 * User without an account will be prompted with an option to sign up
					 * option to sign up.
					 *
					 * @see js/model/register.js
					 */
					if (processState == 'sign-up') {
						_suggestUserSignUp();

					// otherwise, the user is an existing user and needs to be added to the
					// class
					} else if (processState == 'redirect') {
						_enrollUserToClass();
					}

					// populate dialog with new content
					$('.dialog-inner .dialog-content').html(_taskCreationMessage);

					// binding redirect actions to class page
					if (_redirect) {
						$('.dialog-close').live('click', function(e) {
							_goToClassPage(_redirect);
						});
						$('.go-to-class-page').live('click', function(e) {
							_goToClassPage(_redirect);
						});
					}

					// if the user exceed enrollment limit
					var dialogRegion = $('.dialog');
					$('.button.cancel-action').click(function(e) {
						e.preventDefault();
						_goToClassPage('/class');
					});
					ClassRemove.removeClassFromList(dialogRegion, function() {
						var confirmAction = $('.button.confirm-action', dialogRegion);
						confirmAction.removeClass('disabled');
						confirmAction.click(function(e) {
							e.preventDefault();
							$.ajax({
								url: '/college-class-enroll',
								type: 'post',
								async: false,
								data: 'section_id=' + _section_id,
								success: function(response) {
									_goToClassPage(response.redirect);
								}
							});
						});
					});

				}
			});
		});
	};
};
