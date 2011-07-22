$(document).ready(function() {
	window.navigation = $('#navigation-menu');

	navigation.delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		if (target.hasClass('login')) {
			login.submit();
		} else if (target.hasClass('logout')) {
			logout.submit();
		}
	});
	
});
