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
		$('a.remove-class.button', _region).click(function(e) {
			e.preventDefault();
			var content = '<h3>You are about to remove ' + section_code + ' from your schedule.</h3>' + 
			"<p><strong>Remember, you will still have access to the assignments you've created, but the ones shared from this class will be removed from your schedule.</strong></p>" +
			"<h3> You can always add the class back, though.</h3>" +
			'<div class="remove-option">' +
				'<a class="confirm-remove button" href="#">confirm</a>' +
				'<a class="cancel-remove button" href="#">cancel</a>' +
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
 * Offer a list of currently enrolled classes to be removed
 */
ClassRemove.listClassToRemove = function(classList) {
	var html  = '';
	var list  = '';
	var count = 0;
	for (section_id in classList) {
		list += '<li>' + classList[section_id] + '<a href="#" id="' + section_id + '" class="remove-class button">&times;</a></li>';
		count++;
		if (count >=3 && count % 3 == 0) {
			html += '<ul>' + list + '</ul>';
			list = '';
		} 
	}
	return '<div class="class-remove-list">' + html + '</div>';
};
/**
 * Remove class from a list
 */
ClassRemove.removeClassFromList = function(region, callback) {
	$('a.remove-class.button', region).click(function(e) {
		e.preventDefault();
		var section_id = $(this).attr('id');
		$(this).parent('li').remove();
		ClassRemove.removeClass(section_id, callback);
	});
}
/**
 * Remove user from class
 *
 * @param int section_id
 * @param object callback
 *  an action to be executed after the class is removed
 */
ClassRemove.removeClass = function(section_id, callback) {
	$('.dialog').click(function(e) {
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
