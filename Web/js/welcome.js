$(document).ready(function() {
	window.welcome = $('.welcome');

	welcome.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		updatePageHeight();
		var target = $(this);
		if (target.hasClass('register')) {
			register.submit();
		} else if (target.hasClass('upload')) {
			register.init();
		} else if (target.hasClass('enroll')) {
		}
	});
		welcome.delegate('a.register', 'click', function(e) {
	});
});
