/**
 * @file
 * Handle event inputs on /home and lazy load contents to reduce load time
 *
 * @see js/model/task.js
 */
$P.ready(function() {
	// Initilize the date-time selector
	$('#time-picker').datetime({  
		duration: '15',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		time24h: false  
	});  

	// Load tasks
	task.init();
	var userTaskOption = $('#user-list-task-option');
	$('input[name=paginate]', userTaskOption).val(0);
	var agendaPanel = $('.panel-01 .panel-inner');
	agendaPanel.after('<a href="#" class="button more">more</a>');
	task.getTaskBelongToUser(userTaskOption, agendaPanel);

	// toggle task creation form
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
	blurInput(body);

	body.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
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
			task.submit(agendaPanel);
			agendaPanel.empty('');
			task.loading();
			task.getTaskBelongToUser(userTaskOption, agendaPanel);
		} else if (target.hasClass('more')) {
			task.incrementPaginate(userTaskOption);
			task.getTaskBelongToUser(userTaskOption, agendaPanel);
		}
	});
});
