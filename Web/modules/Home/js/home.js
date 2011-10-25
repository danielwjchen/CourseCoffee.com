/**
 * @file
 * Handle event inputs on /home and lazy load contents to reduce load time
 *
 * @see js/model/toDo.js
 */
$P.ready(function() {

	/**
	var classFeed = $('form[name=class-feed]');
	var institution_uri = $('input[name=institution_uri]', classFeed).val();
	var year = $('input[name=year]', classFeed).val();
	var term = $('input[name=term]', classFeed).val();
	$('input[name=section-code]', classFeed).each(function(i, e) {
		var pieces = $(e).val().split('-');
		var crs_sec = pieces[1].split(' ');
		classFeed.append('<h3>' + $(e).val() + '</h3><hr /><fb:comments href="' +
			window.location.origin + '/class/' +
			institution_uri + '/' +
			year + '/' +
			term + '/' +
			pieces[0] + '/' +
			crs_sec[0] + '/' +
			crs_sec[1] + '" num_posts="3" width="460"></db:comments>');
	});
	if (window.FB !== undefined) {
		FB.XFBML.parse(document.getElementById(id));
	}

	var domain = window.location.protocol + '//' + window.location.hostname;
	$('.panel-02 .panel-inner').append('<fb:activity site="' + domain + '" width="460" height="300" header="false" recommendations="true">' +
	'</fb:activity>');
	//<fb:comments href="' + domain + '" num_posts="10" width="459"></fb:comments>');
	if (window.FB !== undefined) {
		FB.XFBML.parse(document.getElementById(id));
	}
	*/

	/**
	if ($('.profile img').height() > 0) {
	}
	*/

	// Initilize the date-time selector


	/**
	 * Ask user to link with facebook if a profile image doesn't exist
	 */
	var profileImage = $('img.profile-image');
	if (profileImage.attr('src').indexOf("default") != -1) {
		profileImage.after('<a href="#" class="facebook button">connect with facebook</a>');
		$('.panel-top .facebook.button').click(function(e) {
			e.preventDefault();
			dialog.open('connect-facebook', "<h2>Collaborate with your classmates on facebook</h2>" +
				'<div class="dialog-content">' +
					"<dl>" +
						'<img src="/images/connect-with-facebook.png" />' +
						"<dt>Linking your account does more than putting a face to your profile. You will be able to...</dt>" +
						'<dd><span class="orange-checker">&#10004;</span> Get help on your homework</dd>' +
						'<dd><span class="orange-checker">&#10004;</span> Share notes</dd>' +
						'<dd><span class="orange-checker">&#10004;</span> Trade used textbooks</dd>' +
					'</dl>' +
					"<h2>It just makes sense.</h2>" +
					'<a href="#" class="button facebook">connect with facebook</a>' +
					'<span class="double-underline"><a class="cancel" href="#">cancel</a></span>' +
				'</div>');
			$('.dialog').delegate('a', 'click', function(e) {
				e.preventDefault();
				var target = $(this);
				if (target.hasClass('dialog-close') || target.hasClass('cancel')) {
					dialog.close();
				} else if (target.hasClass('facebook')) {
					$FB(function() {
						FB.login(
							function(response) {
								if (response.authResponse) {
									$.ajax({
										url: '/user-link-facebook',
										type: 'POST',
										cache: false,
										data: 'fb_uid=' + response.authResponse.userID,
										success: function(response) {
											if (response.redirect) {
												window.location = response.redirect;
											}
										}
									});
								}
							}
						);
					});
				}
			});

		});
	}

	// Load to-dos
	var panelMenu = $('.panel-menu');
	var taskInfoMenu = $('#task-info-menu');
	var toDoList = $('#to-do-list');
	var filter = $('#to-do-option input[name=filter]');
	var resetTaskInfoMenu = function(active) {
		$('li', taskInfoMenu).removeClass('active');
		$('#' + active, taskInfoMenu).addClass('active');
		$('input[name=paginate]').val(0);
		$('.more').removeClass('disabled');
	}
	var toDo = new ToDo('#to-do-option', '#to-do-list', '#to-do-creation-form');
	toDo.populate();

	// toggle toDo creation form
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
	blurInput(body);

	var bookList = new BookSuggest('#home-book-list');
	bookList.getAllBookList('.panel-01 input[name=section_id]');

	/**
	 * Filter task by option
	 */
	taskInfoMenu.delegate('li', 'click', function(e) {
		var target = $(this);
		var selected = target.attr('id');
		var optionForm = $('#to-do-option');
		resetTaskInfoMenu(selected);
		if (selected == 'option-pending') {
			filter.val('pending');
		} else if(selected == 'option-finished') {
			filter.val('finished');
		} else if(selected == 'option-all') {
			filter.val('all');
		}
		
		toDoList.empty();
		toDo.populate();
	});


	body.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('submit')) {
			toDo.createTask();

		} else if (target.hasClass('more')) {
			toDo.incrementPaginate();
			toDo.populate();
		}
	});
});
