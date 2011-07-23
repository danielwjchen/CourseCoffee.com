$P.ready(function() {
	window.navigation = $('#navigation-menu');

	// hide/show the default input
	blurInput(navigation);

	navigation.delegate('a.button', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('login')) {
			e.preventDefault();
			login.submit();
		} else if (target.hasClass('logout')) {
			e.preventDefault();
			logout.submit();
		}
	});
	
});
