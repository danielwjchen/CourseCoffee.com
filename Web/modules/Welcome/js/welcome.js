/**
 * @file
 * Manage user events and their corresponding javascript actions on welcome 
 * page
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
$P.ready(function() {
	blurInput('.login-form');
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

	var slideShow = new SlideShow('.slide-show');
	slideShow.perform();

	var welcome = $('.welcome');
	$('#school-options', welcome).change(function(e) {
		window.location = window.location.protocol + '//' + $(this).val();
	});

	// submit login form on press enter
	$('#user-login-form input').keypress(function(e){
		if(e.which == 13){
			login.submit();
		}
	});
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
			var modal = new Modal();
			modal.open('sign-up', content);
			$('.modal-close').live('click', function(e) {
				e.preventDefault();
				modal.close();
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
		} else if(target.hasClass('regular')) {
			window.location = target.attr('href');
		}
	});
});
