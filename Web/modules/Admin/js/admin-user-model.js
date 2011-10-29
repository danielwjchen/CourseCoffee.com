/**
 * @file
 * Interact with user to moderate syllabus
 */
window.AdminUser = function(optionMenu, optionFormClassName, regionClassName) {
	var _menu   = $(optionMenu);
	var _option = $(optionFormClassName);
	var _region = $(regionClassName);
	var _status = {
		'new'      : 'has_syllabus',
		'approved' : 'approved',
		'removed'  : 'removed',
		'all'      : 'all',
	}

	/**
	 * Reset user sub-menu
	 */
	this.resetMenu = function() {
		$('input[name=status]', _option).val(_status.new);
	};


	/**
	 * Get a list of syllabi
	 *
	 * @param object optionFormClassName
	 * @param object regionClassName
	 */
	this.getList = function() {
		$.ajax({
		data: _option.serialize(),
		success: function(response) {
			}
		});
	};
};
