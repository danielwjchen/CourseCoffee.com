/**
 * @file
 * Log out user
 */
window.logout = {
	/**
	 * submit the user logout request
	 */
	'submit' : function() {
		$.get('user/logout', function(response) {
			if (response.redirect) {
				window.location = response.redirect;
			}
		});
	}
};

