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
	var classBookList = $('#class-book-list');

	var classInfo = new ClassInfo('.class-section-info', '#class-option-form', '#class-task-list', '#class-task-creation-form');

	// mark the first item in class list as active on page load
	var defaultMenuOption = 'a:first';
	var defaultClassId = classInfo.getClassId();
	if (defaultClassId) {
		defaultMenuOption = '#' + defaultClassId;
	}

	$(defaultMenuOption, panelMenu).addClass('active');
	var sectionId = $(defaultMenuOption, panelMenu).attr('id');
	var bookList = new BookSuggest('#class-book-list');

	// debug
	// console.log('default class id - ' + defaultClassId);

	classInfo.getClassInfo(sectionId);
	classInfo.populate();
	bookList.getBookList(sectionId);

	// Load tasks
	var task = new Task('#task-creation-form');
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-02 .panel-inner .task-list');

	// Over see inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
		e.preventDefault();
		var target = $(this);

		var selectedSectionId = target.attr('id');
		classInfo.getClassInfo(selectedSectionId);
		bookList.getBookList(selectedSectionId);

		// debug
		// console.log(selectedBookListId);

		$('.option', panelMenu).removeClass('active');
		target.addClass('active');
		agendaPanel.empty();
	});

	blurInput(body);

	// Over see inputs in task panel
	$('.panel-02', body).delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);

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
