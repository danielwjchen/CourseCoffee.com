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

	// Load to-dos
	var panelMenu = $('.panel-menu');
	var taskInfoMenu = $('#task-info-menu');
	var toDoList = $('#to-do-list');
	var filter = $('#to-do-option input[name=filter]');
	var resetTaskInfoMenu = function(active) {
		$('li', taskInfoMenu).removeClass('active');
		$('#' + active, taskInfoMenu).addClass('active');
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
