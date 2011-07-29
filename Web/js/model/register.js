/**
 * @file
 * Handle user registraion
 */
window.register = {
	/**
	 * Initialize the user registration form
	 */
	'init': function () {
		$.get('?q=user/register', function(data) {
			$('.body').html(register.form('', '', '', data.token, ''));
		});
	},
	/**
	 * Generate the HTML form for user registration
	 */
	'form': function(email, password, confirm, token, error) {
		return '<div class="user-registration dialog">' +
			'<form id="user-register-form" name="registration" action="user/register" method="post">' +
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
						'<input type="password" name="confirm" value="' + confirm + '" />' +
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
	},
	/**
	 * Validate the form fields
	 */
	'validate' : function() {
		var error = true;
		$(':input').each(function(i){
			if ($(this).val() == '') {
				register.error('You have empty fileds. Please try again.');
				error = false;
				return;
			}
		});
		
		return error;
	},
	/**
	 * submit the user registration form
	 */
	'submit' : function() {
		var formData = $('#user-register-form').serialize();
		if (register.validate()) {
			$.ajax({
				url: '?q=user-register',
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
	}
};

