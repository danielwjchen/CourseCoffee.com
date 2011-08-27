/**
 * @file
 * Handle user registraion
 */
window.register = {
	/**
	 * Initialize the user registration form
	 */
	'init': function () {
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
		var validateRules = {
			'checkEmpty' : function() {
				var pass = true;
				$(':input').each(function(i){
					if ($(this).val() == '') {
						register.error('You have empty fileds. Please try again.');
						pass = false;
						return ;
					}
				});
				return pass;
			},
			'checkEmail' : function() {
				var validateEmail = function(string) {
					var rule = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					return string.match(rule);
				}

				if (!validateEmail($('input[name=email]').val())) {
					register.error('Please enter a valid email account');
					return false;
				}

				return true;
			},
			/*
			'validateName' : function() {
				var rule  = /^[a-z]{3,17}$/;
				var first = $('input[name=first-name]').val();
				var last  = $('input[name=last-name]').val();
				// worlds' longest surename record has 17 characters
				register.error('Please enter a valid first or last name.');
				return first.match(rule) && last.match(rule);
			},
			*/
			'password' : function() {
				var password = $('input[name=password]').val();
				var confirm  = $('input[name=confirm]').val();
				if (password != confirm) {
					register.error('Password and confirmation do not match.');
					return false;
				}
				if (password.length < 8 || confirm.length < 8) {
					register.error('Password too short. A good password must be a combination of at least 8 alphanumeric characters');
					 return false;
				}

				return true;
			}
		};

		var pass = true;
		for (i in validateRules) {
			if (!validateRules[i]()){
				pass = false;	
				return ;
			}
		}

		
		return pass;
	},
	/**
	 * submit the user registration form
	 */
	'submit' : function() {
		var formData = $('#user-register-form').serialize();
		if (register.validate()) {
			$.ajax({
				url: '/user-register-regular',
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

/**
 * Handle user registration
 */
var SignUp = function(url_param) {
	var _param = url_param;
};

/**
 * Offer options to different sign-up process
 */
SignUp.getOptions = function() {
	return '<div class="sign-up-option">' +
		'<a class="facebook button sign-up" href="/sign-up">sign up with facebook</a>' +
		'<div class="alternative">' +
			'<p>Or, you can always manually create an account...</p>' +
			'<span class="double-underline"><a class="regular sign-up" href="/sign-up">sign up</a></span>' +
		'</div>' +
	'</div>';

}

