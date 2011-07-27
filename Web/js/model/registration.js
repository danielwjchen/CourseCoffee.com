/**
 * @file
 * Handle user registraion
 */
window.register = {
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
	};

