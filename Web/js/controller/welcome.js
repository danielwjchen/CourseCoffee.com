/**
 * @file
 * Manage user events and their corresponding javascript actions on welcome 
 * page
 */
$P.ready(function() {
	window.welcome = $('.welcome');
	blurInput(welcome);

	welcome.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		if (target.hasClass('login')) {
			login.submit();
		} else if (target.hasClass('register')) {
			register.submit();
		} else if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('enroll')) {
		}
	});
});
