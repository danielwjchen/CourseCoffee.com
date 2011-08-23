/**
 * @file
 * Manage user events and their corresponding javascript actions on welcome 
 * page
 */
$P.ready(function() {
	var fbUid = '';
	$FB(function() {
		FB.getLoginStatus(function(response) {
			if (response.authResponse) {
				fbUid = response.authResponse.userID;
				$.ajax({
					url: '/user-login-fb',
					type: 'post',
					cache: false,
					data: 'fb_uid=' + fbUid,
					success: function(response) {
						if (response.success) {
							window.location = response.redirect;
						}
					}
				});
			}
		});
	});

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

		} else if (target.hasClass('upload')) {
			doc.init();
		}
	});
	welcome.delegate('a.sign-up', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		if (target.hasClass('facebook')) {
			window.location = target.attr('href') + '?fb=true&fb_uid=' + fbUid;
		}
	});
});
