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
	var agendaPanel = $('.panel-02 .panel-inner .task-list');

	// Apply scrollbar to the calendar 
	$('.calendar-display').scrollBar();

	// Initialize calendars
	calendar = new Calendar('.calendar-display', '#calendar-option', '#calendar-task-list', '#calendar-task-creation-form');
	calendar.getMonthCalendar();
	calendar.populate();

	// Oversee inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
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
