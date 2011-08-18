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
		} else if (target.hasClass('sign-up')) {
			var signUpOption = SignUp.getOptions();
			var content = '<div class="progress">' +
				'<div class="progress-inner">' +
					'<h3>How would you like to create your account?</h3>' +
					signUpOption +
				'</div>' + 
			'</div>';
			dialog.open('sign-up', content);
			$('.dialog-close').live('click', function(e) {
				e.preventDefault();
				dialog.close();
			});
		} else if (target.hasClass('regular') || target.hasClass('facebook')) {
			window.location = target.attr('href');
		} else if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('enroll')) {
		}
	});
});
