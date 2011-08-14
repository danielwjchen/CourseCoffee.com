/**
 * @file
 * Handle event inputs on /home and lazy load contents to reduce load time
 *
 * @see js/model/toDo.js
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

	// Load to-dos
	toDo = new ToDo('#to-do-option', '#to-do-list', '#to-do-creation-form');
	toDo.populate();
	var agendaPanel = $('.panel-01 .panel-inner');
	agendaPanel.after('');

	// toggle toDo creation form
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
	blurInput(body);

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
