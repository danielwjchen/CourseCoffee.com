/**
 * @file
 * Handle event inputs on /home and lazy load contents to reduce load time
 */
$P.ready(function() {
/**
 * Initilize the date-time selector
 */
	$('#time-picker').datetime({  
		duration: '15',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		time24h: false  
	});  
	/**
	 * Load tasks
	 */
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-01 .panel-inner');
	task.getTaskBelongToUser(userTaskOption, agendaPanel);
	blurInput(body);
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
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
			task.submit();
			task.getTaskBelongToUser(userTaskOption, agendaPanel);

		}
	});
});
