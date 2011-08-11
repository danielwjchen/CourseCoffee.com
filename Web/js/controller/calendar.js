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
	task.init();
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-02 .panel-inner .task-list');

	// Apply scrollbar to the calendar 
	$('.calendar-display').scrollBar();

	// Initialize calendars
	calendar = new Calendar('.calendar-display', '#calendar-option', '.task-list');
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

	// Initilize the date-time selector
	$('#time-picker').datetime({  
		duration: '15',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		time24h: false  
	});  

	// toggle task creation form
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
	blurInput(body);

	// Over see inputs in task panel
	taskRegion = $('.panel-02');
	taskRegion.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);

		if (target.hasClass('show-detail')) {
			if ($('.optional').hasClass('hidden')) {
				$('.optional').removeClass('hidden');
				target.text('less detail');
			} else {
				$('.optional').addClass('hidden');
				target.text('more detail');
			}

		} else if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('submit')) {
			task.submit();
			setTimeout("calendar.populate()", 2000);

		}
	});

});
