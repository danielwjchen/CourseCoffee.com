/**
 * @file
 * Remove user from class
 */
window.ClassRemove = function(regionClassName, section_id) {
	var _region = $(regionClassName);
	var _section_id = section_id;

	/**
	 * Bind action to the remove button
	 *
	 * This method creates a dialog to promopt the user about the action.
	 *
	 * @param int section_id
	 * @param string section_code
	 * @param object callback
	 *  an action to be executed after the class is removed
	 */
	this.promptAction = function(section_code, callback) {
		$('a.remove.button', _region).click(function(e) {
			e.preventDefault();
			var content = '<h3>You are about to remove ' + section_code + ' from your schedule.</h3>' + 
			'<div class="remove-option">' +
				'<a class="confirm-remove button">confirm</a>' +
				'<a class="cancel-remove button">cancel</a>' +
			'</div>';
			dialog.open('remove-class', content);
			$('.dialog a').click(function(e) {
				if ($(this).hasClass('dialog-close') || $(this).hasClass('cancel-remove')) {
					e.preventDefault();
					dialog.close()
				} else if ($(this).hasClass('confirm-remove')) {
					ClassRemove.removeClass(_section_id, callback);
				}
			});
		});
	};

};
/**
 * Remove user from class
 *
 * @param int section_id
 * @param object callback
 *  an action to be executed after the class is removed
 */
ClassRemove.removeClass = function(section_id, callback) {
	$('.dialog').click(function(e) {
		console.log(this);
		console.log(section_id);
		e.preventDefault();
		$.ajax({
			url: '/college-class-remove',
			type: 'post',
			cache: true,
			data: 'section_id=' + section_id,
			success: function(response) {
				if (response.success) {
					callback();
				}
			}
		});
	});
};
