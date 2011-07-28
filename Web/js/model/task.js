/**
 * @file
 * Manage access to tasks and creation
 */
window.task = {
	/**
	 * Set error messages
	 */
	'error': function(message) {
	},
	/**
	 * submit new task
	 */
	'submit': function() {
		formFields = $('#new-task-form');
		if ($('input[name=objective]').val() == '' || $('input[name=objective]').val() == $('input[name=objective]').attr('default') || $('input[name=due_date]').val() == '') {
			task.error('You have empty fileds. Please try again.');
			return ;
		}
		if ($('textarea[name=description]').val() == $('textarea[name=description]').attr('default')) {
			$('textarea[name=description]').val('');
		}
		var formData = $('#new-task-form').serialize();
		$.ajax({
			url: '?q=task-add',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				if (response.token) {
					$('input[name=token]').attr('value', response.token);
				} 
				if (response.error) {
					task.error(response.error);
				} 
				if (response.success) {
					// restore default value on submission
					$(':input', formFields).each(function(index){
						if ($(this).attr('default')) {
							$(this).val($(this).attr('default'));
						}
					});
					// hide the form details...
					$('.additional').addClass('hidden');
					$('.optional').addClass('hidden');
					$('.show-detail').text('mode detail');
				}
			}
		});
	},
	/**
	 * Get list item in a HTML
	 *
	 * @param objective
	 * @param dueDate
	 * @param location
	 * @param description
	 *
	 * @return
	 */
	'getListItem': function(objective, dueDate, location, description) {
		var html = "<li><dl>";
		html += "<dt>" + objective + "</dt>";
		html += "<dd class='due_date'>" + dueDate + "</dd>";
		html += location != null ? "<dd class='location'>" + location + "</dd>" : "";
		html += description != null ? "<dd class='description'>" + description + "</dd>" : "";
			
		html += "</dl></li>";
		return html;
	},
	/**
	 * Get tasks belong to a user
	 *
	 * @params option
	 *  a form of options to be serializd
	 * @param region
	 *  a region to update the content
	 */
	'getTaskBelongToUser': function(option, region) {
		var formData = option.serialize();
		$.ajax({
			url: '?q=user-list-task',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				if (response.success) {
					var html = "<div class='task'><div class='task-inner'><ul>";
					// if there is only one single item.
					if (response.list['id'] != undefined) {
						html += task.getListItem(response.list['objective'], 
							response.list['due_date'],
							response.list['location'],
							response.list['description']
						);
					} else {
						for (i in response.list) {
							html += task.getListItem(response.list[i]['objective'], 
								response.list[i]['due_date'],
								response.list[i]['location'],
								response.list[i]['description']
							);
								
							html += "</dl></li>";
						}
					}

					html += "</ul></div></div>";
					region.html(html);
				}
			}
		});
	},
	/**
	 * Get tasks belong to a class
	 */
	'getTaskBelongToClass': function() {
	},
	/**
	 * Get tasks belong to a time period
	 */
	'getTaskBelongToDate': function() {
	}
}
