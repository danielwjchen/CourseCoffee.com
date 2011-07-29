/**
 * @file
 * Manage user events and their corresponding javascript actions on sign-up 
 * page
 */
$P.ready(function() {
	window.signUp = $('.sign-up');

	signUp.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		if (target.hasClass('sign-up')) {
			register.submit();
		}
	});
});
