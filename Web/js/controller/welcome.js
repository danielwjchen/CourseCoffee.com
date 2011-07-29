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
			/**
			 * @todo
			 * this is a dirty hack. the only reason it's here is because I am too 
			 * lazy to write another dialog pop at the moment. Someone can implmenet 
			 * it, and you will be a hero.
			 */
			window.location = target.attr('href');
		} else if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('enroll')) {
		}
	});
});
