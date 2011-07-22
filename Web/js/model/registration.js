/**
 * @file
 * Handle user registraion
 */
window.register = {
	/**
	 * Initialize the user registration form
	 */
	'init': function () {
		$.get('user/register', function(data) {
			$('.body').html(register.form('', '', '', data.token, ''));
		});
	},
	/**
	 * Generate the HTML form for user registration
	 */
	'form': function(email, password, confirm, token, error) {
		return '<div class="user-registration dialog">' +
			'<form id="user-registration-form" name="registration" action="user/register" method="post">' +
				'<input type="hidden" name="token" value="' + token + '" />' +
				'<div class="row error hidden"></div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="user-account">email: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="text" name="email" value="' + email + '" />' +
					'</div>' +
				'</div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="password">password: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="password" name="password" value="' + password + '" />' +
					'</div>' +
				'</div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="confirm">confirm password: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="confirm" name="confirm" value="' + confirm + '" />' +
					'</div>' +
				'</div>' +
				'<a href="#" class="button register">Join</a>' +
			'</form>' +
		'</div>';
	},
	/**
	 * Set error messages
	 */
	'error' : function(message) {
		var error = $('.error');
		error.html('<p>' + message + '</p>');
		error.removeClass('hidden');
		$('.dialog').height(364);
	},
	/**
	 * submit the user registration form
	 */
	'submit' : function() {
		if ($('input[name=email]').val() == '' || $('input[name=password]').val() == '' || $('input[name=confirm]').val() == '') {
			register.error('You have empty fileds. Please try again.');
			return ;
		}
		var formData = $('#user-registration-form').serialize();
		$.ajax({
			url: 'user/register',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				if (response.error) {
					register.error(response.error);
				} 
				if (response.token) {
					$('input[name=token]').attr('value', response.token);
				}
				if (response.redirect) {
					window.location = response.redirect;
				}
			}
		});
	}
	};

