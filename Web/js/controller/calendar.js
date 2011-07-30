$P.ready(function() {
	panelMenu = $('.panel-menu');
	timestamp = $('input[name=timestamp]', panelMenu).val();
	displayType= $('input[name=type]', panelMenu).val();
	calendar.init(displayType, timestamp);

	panelMenu.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);
		$('.option', panelMenu).removeClass('active');
		target.addClass('active');
		if (target.hasClass('today')) {
			calendar.getDayCalendar(timestamp, 1);
		} else if (target.hasClass('customized')) {
			// we might have customizable calendar view in the future, but for now 
			// it's hard-coded to 3
			calendar.getDayCalendar(timestamp, 4);
		} else if (target.hasClass('week')) {
			calendar.getDayCalendar(timestamp, 7);
		} else if (target.hasClass('month')) {
			calendar.getMonth(timestamp);
		}
	});
});
