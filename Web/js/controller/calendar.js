/**
 * @file
 * Handle event inputs on /calendar and lazy load contents to reduce load time
 */
$P.ready(function() {
	panelMenu = $('.panel-menu');

	// Load tasks
	task.init();
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-02 .panel-inner .task-list');
	task.getTaskBelongToUser(userTaskOption, agendaPanel);

	// Initialize calendars
	timestamp = $('input[name=timestamp]', panelMenu).val();
	displayType= $('input[name=type]', panelMenu).val();
	calendar.init(displayType, timestamp);

	// Over see inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);
		$('.option', panelMenu).removeClass('active');
		target.addClass('active');

		if (target.hasClass('today')) {
			calendar.getDayCalendar(timestamp, 1);

		} else if (target.hasClass('customized')) {
			// we might have customizable calendar view in the future, but for now 
			// it's hard-coded to 4
			calendar.getDayCalendar(timestamp, 4);
			calendar.update();

		} else if (target.hasClass('week')) {
			calendar.getWeekCalendar(timestamp);
			calendar.update();

		} else if (target.hasClass('month')) {
			calendar.getMonthCalendar(timestamp);
			calendar.update();
		}

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
			task.getTaskBelongToUser(userTaskOption, agendaPanel);

		}
	});

});
