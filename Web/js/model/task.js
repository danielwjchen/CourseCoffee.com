/**
 * @file
 * Manage access to tasks and creation
 */
window.task = {
	/**
	 * Set error messages
	 */
	'init' : function() {
		formFields = $('#new-task-form');
		$.ajax({
			url: '/task-init',
			type: 'POST',
			success: function(response) {
				if (response.token) {
					$('input[name=token]', formFields).attr('value', response.token);
				} 
			}
		});
	},
	'error': function(message) {
	},
	/**
	 * show loading meter
	 */
	'loading' : function(region) {
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
			url: '/task-add',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				if (response.token) {
					$('input[name=token]', formFields).attr('value', response.token);
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
	 * Increment the paginate value
	 *
	 * @params option
	 *  a form of options to be serializd
	 */
	'incrementPaginate' : function(option) {
		paginate = $('input[name=paginate]', option);
		paginate.val(parseInt(paginate.val()) + 1);
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
	'getListItem': function(item) {
		var html = "<li><dl>";

		if (item['subject_abbr'] && item['course_num']) { 
			html += "<dt>" + item['subject_abbr'] + '-' + item['course_num'] + "</dt>";
			html += "<dd>" + item['objective'] + "</dd>";
		} else {
			html += "<dt>" + item['objective'] + "</dt>";
		}

		html += "<dd id='" + item['due_date'] + "' class='due_date count-down'>" + item['due_date'] + "</dd>";
		html += item['location'] != null ? "<dd class='location'>" + item['location'] + "</dd>" : "";
		html += item['description'] != null ? "<dd class='description'>" + item['description'] + "</dd>" : "";
			
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
		region.addClass('loading');
		var formData = option.serialize();
		$.ajax({
			url: '/user-list-task',
			type: 'POST',
			cache: false,
			data: formData,
			success: function(response) {
				region.removeClass('loading');
				if (response.success) {
					task.generateList(response.list, region);
				}
			}
		});
	},
	/**
	 * Generate a list of task
	 *
	 * @param object list
	 *  a JSON list retrieved from the server
	 * @param region
	 *  a region to update the content
	 */
	'generateList' : function(list, region) {
		// if user has nothing to do
		if (list == null) {
			html = "<h3 class='no-task'>hmmm..... you don't have anything to do at the moment. hooray?</h3>";
			$('.button.more').addClass('disabled');
		} else {
			html = '';
			// if there is only one single item.
			if (list['id']) {
				html += task.getListItem(list);
			} else {
				for (i in list) {
					html += task.getListItem(list[i]);
				}
			}

		}
		hasTask = $('.task li');

		if (hasTask.length == 0) {
			html = "<div class='task'><div class='task-inner'><ul>" +
				html +
			"<ul></div></div>" ;
			region.html(html);
		} else {
			$('li:last', region).after(html);
		}
		$('.count-down', region).each(function(i) {
			$(this).translateTime();
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
