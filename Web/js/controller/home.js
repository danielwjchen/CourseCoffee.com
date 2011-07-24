/**
 * @file
 * Handle event inputs on /home and lazy load contents to reduce load time
 */
$P.ready(function() {
	/**
	 * Load tasks
	 */
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-01 .panel-inner');
	task.getTaskBelongToUser(userTaskOption, agendaPanel);
	updatePageHeight();
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
		}
		if (target.hasClass('submit')) {
			task.submit();
			task.getTaskBelongToUser(userTaskOption, agendaPanel);

		}
	});
});
