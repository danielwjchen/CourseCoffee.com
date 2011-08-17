/**
 * @file
 * Suggest a list of class base on input string and enroll user to the best match
 *
 * This is a child class of ClassSuggest
 * @see js/model/class-suggest.js
 */
window.ClassEdit = function(formName, inputName) {

	var form = $(formName);

	/**
	 * Submit the suggested class and enroll user
	 */
	var confirmEdit = function(section_id) {
		if (section_id != undefined) {
			$('#section-id', form).val(section_id);
			$('.confirm', form).removeClass('disabled');
		}
	};

	var classSuggest = new ClassSuggest(formName, inputName, confirmEdit);

	/**
	 * Edit the user to class
	 */
	this.submit = function() {
		submitEdit();
	}

};
