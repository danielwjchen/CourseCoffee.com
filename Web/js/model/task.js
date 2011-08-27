/**
 * @file
 * Manage access to task and task creation
 *
 * This is a base class for calendar, class, and to-do. Child classes must 
 * define method populate() and getTaskList(), and extend createTask() and 
 * incrementPaginate().
 *
 * I wish javascript supports OOP.
 *
 */
window.Task = function(creationFormName, optionFormName) {
	var creationForm = $(creationFormName);
	var optionForm   = $(optionFormName);

	// toggle task creation form
	$('input.objective', creationForm).live('click', function(e) {
		$('.additional', creationForm).removeClass('hidden');
	});

	creationForm.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);
		// toggle task creation form detail
		if (target.hasClass('show-detail')) {
			if ($('.optional').hasClass('hidden')) {
				$('.optional').removeClass('hidden');
				target.text('less detail');
			} else {
				$('.optional').addClass('hidden');
				target.text('more detail');
			}
		}
	});

	$.ajax({
		url: '/task-init',
		type: 'POST',
		cache: false,
		success: function(response) {
			if (response.token) {
				$('input[name=token]', creationForm).attr('value', response.token);
			} 
		}
	});

	// Initilize the date-time selector
	$('#time-picker').datetime({  
		duration: '15',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		time24h: false  
	});  

	/**
	 * Add URL to each task
	 */
	this.AddUrlToTask = function(list) {
		// debug
		// console.log(response.list);

		var domain = 'http://' + window.location.hostname;
		var institution_uri = $('input[name=institution-uri]', optionForm).val();
		var year = $('input[name=year]', optionForm).val();
		var term = $('input[name=term]', optionForm).val();
		var tasks = {};

		if (list['id']) {
			tasks['0'] = list;
		} else {
			tasks = list;
		}

		$.each(tasks, function(i, task) {
			/*
			if (task && task['subject_abbr'] && task['course_num'] && task['section_num']) {
				tasks[i]['url'] = domain + '/class/' + 
					institution_uri + '/' +
					year + '/' + term + '/' +
					task['subject_abbr'] + '/' + 
					task['course_num'] + '/' +
					task['section_num'] + '/' +
					task['id'];
			} else if (task) {
				tasks[i]['url'] = domain + '/task/' + task['id'];
			}
			*/

			tasks[i]['url'] = domain + '/task/' + task['id'];
		});

		return tasks;
	};

	/**
	 * Set error messages
	 */
	this.setError = function(message, region) {
		region.append("<h3 class='error'>" +
			message + 
		"</h3>");
		$('.button.more').addClass('disabled');
	};

	/**
	 * Clear option
	 */
	this.clearOption = function(option) {
		$('input[name=paginate]', option).val(0);
		$('.button.more').removeClass('disabled');
	};

	/**
	 * Increment the paginate value
	 */
	this.incrementPaginate = function() {
		var paginate = $('input[name=paginate]', optionForm);

		// debug
		// console.log(paginate);

		paginate.val(parseInt(paginate.val()) + 1);
	};

	/**
	 * Create task
	 *
	 * @param function callback
	 *  a method to be excuted once the task is created
	 */
	this.createTask = function(callback) {
		if ($('input[name=objective]', creationForm).val() == '' || $('input[name=objective]', creationForm).val() == $('input[name=objective]', creationForm).attr('default') || $('input[name=due_date]', creationForm).val() == '') {
			setError('You have empty fileds. Please try again.');
			return ;
		}

		if ($('textarea[name=description]', creationForm).val() == $('textarea[name=description]', creationForm).attr('default')) {
			$('textarea[name=description]', creationForm).val('');
		}

		$.ajax({
			url: '/task-add',
			type: 'POST',
			cache: false,
			data: creationForm.serialize(),
			success: function(response) {
				if (response.token) {
					$('input[name=token]', creationForm).attr('value', response.token);
				} 
				if (response.error) {
					setError(response.error);
				} 
				if (response.success) {
					// restore default value on submission
					$(':input', creationForm).each(function(index){
						if ($(this).attr('default')) {
							$(this).val($(this).attr('default'));
						}
					});
					// hide the form details...
					$('.additional').addClass('hidden');
					$('.optional').addClass('hidden');
					$('.show-detail').text('mode detail');

					setTimeout(callback(), 500);
				}
			}
		});
	};

}


/**
 * Generate a list of task
 *
 * This is a statis method
 *
 * @param object list
 *  a JSON list retrieved from the server
 * @param object region
 *  a HTML region to be populated with data
 */
Task.generateList = function(list, region) {

	/**
	 * Get list item in a HTML
	 *
	 * @param array item
	 *  - objective
	 *  - due_date
	 *  - location
	 *  - description
	 *
	 * @return html
	 */
	var getListItem = function(item) {
		var html = "<li><dl id='" + item['id'] + "'>";

		if (item['subject_abbr'] && item['course_num']) { 
			html += "<dt>" + item['subject_abbr'] + '-' + item['course_num'] + "</dt>";
			html += "<dd>" + item['objective'] + "</dd>";
		} else {
			html += "<dt>" + item['objective'] + "</dt>";
		}

		html += "<dd id='" + item['due_date'] + "' class='due_date count-down'>" + item['due_date'] + "</dd>";
		html += item['location'] != null ? "<dd class='location'>" + item['location'] + "</dd>" : "";
		html += "<dd class='comment'><a href='" + item['url'] + "' id='" + item['id'] + "' class='fb-comment'>comments &#187;</a></dd>";
		if (item['status'] == 'done') {
			html += "<dd class='status done'>" + item['stats'] + " done!</dd>";
		} else {
			html += "<dd class='status button'><a href='#' taskId='" + item['id'] + "'>&#10004; done</a></dd>";
		}
		html += item['description'] != null ? "<dd class='description'>" + item['description'] + "</dd>" : "";
			
		html += "</dl></li>";
		return html;
	};

	var html = '';

	// if there is only one single item.
	if (list['id']) {
		html += getListItem(list);
	} else {
		for (i in list) {
			html += getListItem(list[i]);
		}
	}


	if ($('li', region).length == 0) {
		html = "<div class='task-list-inner'>" + 
			"<ul>" +
				html +
			"<ul>" + 
		"</div>";

		region.html(html);
	} else {
		$('li:last', region).after(html);
	}

	$('.count-down', region).each(function(i) {
		$(this).translateTime();
	});

	var commentPanel = new CommentPanel();

};

/**
 * Update task status
 */
TaskUpdater = function(regionName) {
	var _region = $(regionName);
	$('dd.status.button', _region).click( function(e) {
		e.preventDefault();
		var target = $(this);
		var taskId = $('a', target).attr('taskId');
		target.removeClass('button').addClass('done');
		$.ajax({
			url: '/task-status-update',
			type: 'POST',
			cache: false,
			data: 'task_id=' + taskId,
			success: function(response) {
				if (response.success) {
					target.html(response.stats + " done!");
				}
			}
		});


	});

}
