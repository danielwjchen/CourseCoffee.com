/**
 * @file
 * Log in user
 */
window.login = {
	/**
	 * Set error messages
	 */
	'error' : function(message) {
		var error = $('.login.error');
		error.html('<p>' + message + '</p>');
		error.removeClass('hidden');
	},
	/**
	 * submit the user login form
	 */
	'submit' : function() {
		if ($('input[name=email]').val() == '' || $('input[name=email]').val() == 'email' || $('input[name=password]').val() == '' || $('input[name=password]').val() == 'password') {
			login.error('You have empty fields. Please try again.');
			return ;
		}
		var formData = $('#user-login-form').serialize();
		$.ajax({
			url: '/user-login',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				if (response.error) {
					login.error(response.error);
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

