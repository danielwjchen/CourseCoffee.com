/**
 * @file
 * Oversee user inputs on navigation area
 */
$P.ready(function() {
	// hide/show the default input
	var fbUid = '';
	$FB(function() {
		FB.getLoginStatus(function(response) {
			if (response.authResponse) {
				fbUid = response.authResponse.userID;
			}
		});
	});
	
	blurInput('#user-login-form');

	// submit login form on press enter
	$('#user-login-form input').keypress(function(e){
		if(e.which == 13){
			login.submit();
		}
	});

	$('.visitor-header').delegate('a.button', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('login')) {
			e.preventDefault();
			login.submit();

		} else if (target.hasClass('sign-up')) {
			e.preventDefault();
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

			$('.dialog').delegate('a.sign-up', 'click', function(e) {
				e.preventDefault();
				var target = $(this);
				if (target.hasClass('facebook')) {
					window.location = target.attr('href') + '?fb=true&fb_uid=' + fbUid;
				} else if(target.hasClass('regular')) {
					window.location = target.attr('href');
				}
			});
		}

	});
	
});
