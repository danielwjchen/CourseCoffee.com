/**
 * @file
 * Oversee user inputs on navigation area
 */
$P.ready(function() {
	window.navigation = $('#navigation-menu');
	var classEnroll = new ClassEnroll('#class-suggest-form', '#suggest-input');

	var pathArray = window.location.pathname.split('/');
	$('li.' + pathArray[1], navigation).addClass('active');


	// hide/show the default input
	blurInput('#user-login-form');

	navigation.delegate('a.button', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('login')) {
			e.preventDefault();
			login.submit();
		} else if (target.hasClass('suggest')) {
			classEnroll.enroll();

		} else if (target.hasClass('logout')) {
			e.preventDefault();
			logout.submit();
		}
	});
	
});
