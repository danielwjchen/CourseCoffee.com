/**
 * @file
 * Handle event inputs on /calendar and lazy load contents to reduce load time
 *
 * @see js/model/task.js
 * @see js/model/calendar.js
 * @see js/model/doc.js
 *
 */
$P.ready(function() {
	panelMenu = $('.panel-menu');

	// Load tasks
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('#calendar-task-list');

	/**
	 * Task info menu
	 */
	var taskInfoMenu = $('#task-info-menu');
	var filter = $('#calendar-option input[name=filter]');
	var resetTaskInfoMenu = function(active) {
		$('li', taskInfoMenu).removeClass('active');
		$('#' + active, taskInfoMenu).addClass('active');
	}
	var calendarTaskList = $('#calendar-task-list');

	// Apply scrollbar to the calendar 
	// $('.calendar-display').scrollBar();

	// Initialize calendars
	calendar = new Calendar('.calendar-display', '#calendar-option', '#calendar-task-list', '#calendar-task-creation-form');
	calendar.getMonthCalendar();
	calendar.populate();

	// Oversee inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
		resetTaskInfoMenu('option-pending');
		filter.val('pending');
		e.preventDefault();
		target = $(this);
		$('.option', panelMenu).removeClass('active');
		target.addClass('active');
		agendaPanel.empty();

		if (target.hasClass('today')) {
			calendar.getDayCalendar(1);

		// we might have customizable calendar view in the future, but for now 
		// it's hard-coded to 4
		} else if (target.hasClass('customized')) {
			calendar.getDayCalendar(4);

		} else if (target.hasClass('week')) {
			calendar.getWeekCalendar();

		} else if (target.hasClass('month')) {
			calendar.getMonthCalendar();

		}

		calendar.populate();

	});

	/**
	 * Filter task by option
	 */
	taskInfoMenu.delegate('li', 'click', function(e) {
		var target = $(this);
		var selected = target.attr('id');
		var optionForm = $('#calendar-option');
		resetTaskInfoMenu(selected);
		if (selected == 'option-pending') {
			filter.val('pending');
		} else if(selected == 'option-finished') {
			filter.val('finished');
		} else if(selected == 'option-all') {
			filter.val('all');
		}
		
		calendarTaskList.empty();
		calendar.populate();
	});

	blurInput(body);

	// Over see inputs in task panel
	taskRegion = $('.panel-02', body);

	taskRegion.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);

		if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('submit')) {
			calendar.createTask();

		} else if (target.hasClass('more')) {
			calendar.incrementPaginate();
			calendar.populate();
		}
	});

});
