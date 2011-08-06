/**
 * @file
 * Oversee class suggestion
 *
 * This is the first module to be written using JavaScript's class implementation
 */
window.ClassSuggest = function(formName) {
	form = $(formName);
	/**
	 * Get suggestions
	 */
	this.suggest = function(string) {
	}

	/**
	 * Enroll the user to class
	 */
	this.enroll = function() {
		$.ajax({
			url: 'college-class-enroll',
			type: 'post',
			data: form.serialize(),
			success: function(response) {
				if (response.content) {
					console.log(response);
					dialog.open('enroll', response.content);
				}
				if (response.redirect) {
					window.location = response.redirect;
				}
			}
		});
	}

}
