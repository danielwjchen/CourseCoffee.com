/**
 * @file
 * Handle event inputs on /class and lazy load contents to reduce load time
 *
 * @see js/model/task.js
 * @see js/model/calendar.js
 * @see js/model/doc.js
 *
 */
$P.ready(function() {
	var panelMenu = $('.panel-menu');
	var classInfoMenu = $('#class-info-menu');
	var classBookList = $('#class-book-list');

	var classInfo = new ClassInfo('.class-section-info', '#class-option-form', '#class-task-list', '#class-task-creation-form');

	// mark the first item in class list as active on page load
	var defaultMenuOption = 'a:first';
	var defaultClassId = classInfo.getClassId();
	if (defaultClassId) {
		defaultMenuOption = '#' + defaultClassId;
	}
	/**
	 * remove all active options in class info menu and rest it
	 */
	var resetClassInfoMenu = function(active) {
		$('li', classInfoMenu).removeClass('active');
		$('#' + active, classInfoMenu).addClass('active');
	}

	$(defaultMenuOption, panelMenu).addClass('active');
	var sectionId = $(defaultMenuOption, panelMenu).attr('id');
	var bookList = new BookSuggest('#class-book-list');

	// debug
	// console.log('default class id - ' + defaultClassId);

	classInfo.getClassInfo(sectionId);
	bookList.getBookList(sectionId);

	// Over see inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		resetClassInfoMenu('option-book');

		var selectedSectionId = target.attr('id');
		classInfo.getClassInfo(selectedSectionId);
		bookList.getBookList(selectedSectionId);

		// debug
		// console.log(selectedBookListId);

		$('.option', panelMenu).removeClass('active');
		target.addClass('active');
	});

	var bookCache = new Cache();


	classInfoMenu.delegate('li', 'click', function(e) {
		var target = $(this);
		var url = '';
		var selected = target.attr('id');
		var classInfoContent  = $('.class-info-content');
		var optionForm = $('#class-option-form');
		selectedSectionId = $('input[name=section-id]').val();
		resetClassInfoMenu(selected);
		if (selected == 'option-book') {
			bookList.getBookList(selectedSectionId);

		} else if(selected == 'option-comment') {
			var domain = 'http://' + window.location.hostname;
			var institution_uri = $('input[name=institution-uri]', optionForm).val();
			var year = $('input[name=year]', optionForm).val();
			var term = $('input[name=term]', optionForm).val();
			var sub  = $('input[name=subject-abbr]', optionForm).val();
			var crs  = $('input[name=course-num]', optionForm).val();
			var sec  = $('input[name=section-num]', optionForm).val();
			url = domain + '/class/' + 
				institution_uri + '/' +
				year + '/' + term + '/' +
				sub + '/' + crs + '/' + sec + '/';
			classInfoContent.html('<fb:comments href="' + url + '" num_posts="10" width="459"></fb:comments>');
			$FB(function() {
				FB.XFBML.parse(document.getElementById(selected));
			});

		}
	});

	blurInput(body);

	// Over see inputs in task panel
	$('.panel-02', body).delegate('a', 'click', function(e) {
		e.preventDefault();
		var target = $(this);

		if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('submit')) {
			classInfo.createTask();

		} else if (target.hasClass('more')) {
			classInfo.incrementPaginate();
			classInfo.populate();
		}
	});

});
