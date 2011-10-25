/**
 * @file
 * Interact with user to moderate syllabus
 */
window.AdminSyllabus = function(optionMenu, optionFormClassName, regionClassName) {
	var _menu   = $(optionMenu);
	var _option = $(optionFormClassName);
	var _region = $(regionClassName);
	var _creationForm = $('.task-create-form-wrapper-skeleton').clone();
	_creationForm.removeClass('task-create-form-wrapper-skeleton').addClass('task-create-form-wrapper');

	$('.panel-02').html('<div class="panel-inner">' + 
		//_creationForm.html() +
		'<form name="class-option" id="class-option-form">' +
			'<input type="hidden" name="section_id" />' +
			'<input type="hidden" name="paginate" value="0" />' +
		'</form>' +
		'<div class="class-info" >' +
			'<div class="action">' +
				'<ul>' +
					//'<li><a href="#" class="button download">donwload</a></li>' +
					'<li><a href="#" class="button approve-syllabus">approve syllabus</a></li>' +
					'<li><a href="#" class="button remove-syllabus">remove syllabus</a></li>' +
					//'<li><a href="#" class="button link-section">link sections</a></li>' +
			'</div>' +
		'</div>' +
		'<div id="syllabus-task-list" class="task-list"></div>' +
		'<a href="#" class="button more">more</a>');

	var _getMoreTask = function() {
		$('.panel-02 .button.more').click(function(e) {
			e.preventDefault();
			var paginate = $('input[name=paginate]', _task_option);
			paginate.val(parseInt(paginate.val()) + 1);
			_getTaskList($('input[name=section_id]', _task_option).val());
		});
	}
	_getMoreTask();

	var _task_list = $('.task-list');
	var _task = new Task('#class-task-creation-form', '#class-option-form');
	var _task_option = $('#class-option-form');
	var _status = {
		'new'      : 'has_syllabus',
		'approved' : 'approved',
		'removed'  : 'removed',
		'all'      : 'all',
	}


	/**
	 * Prompt for action when a syllabus is about to be removed
	 */
	var _promptSyllabusRemoval = function(section_id) {
		var content = '<h2>Would you like to remove all tasks belong to this section as well?</h2>' +
			'<p>Please note that tasks manually created by user will also be removed</p>' + 
			'<div class="action">' +
				'<a href="#" class="button yes">yes</a>' +
				'<a href="#" class="button no">no</a>' +
			'</div>';
		dialog.open('syllabus-remove', content);
		$('.dialog a.button').click(function(e) {
			e.preventDefault();
			var target = $(this);
			if (target.hasClass('yes')) {
				$.ajax({
					url: '/admin-remove-all-quest',
					type: 'POST',
					cache: false,
					data: 'section_id=' + section_id,
					success: function(response) {
						window.location = '/admin';
					}
				});
			}

			dialog.close();
		});
	};

	/**
	 * Highlight selected class 
	 *
	 * @param object selected
	 *  the selected item on queue list
	 */
	var _highlightSelected = function(selected) {
		$('li', _region).removeClass('active');
		selected.addClass('active');
	}

	var _highlightFirstItemInQueue = function() {
		var section_id = $('input[name=section_id]:first', _region).val();
		_highlightSelected($('input[value=' + section_id + ']', _region).parents('li'));
	}

	/**
	 * Bind syllabus admin actions to newly loaded class
	 */
	var _bindSyllabusAdminAction = function() {
		$('.class-info .action .button').click(function(e) {
			e.preventDefault();
			var target = $(this);
			var section_id = $('#class-option-form input[name=section_id]').val();
			if (target.hasClass('download')) {
			} else if (target.hasClass('approve-syllabus')) {
				$.ajax({
					url: '/admin-syllabus-status',
					type: 'POST',
					cache: false,
					data: 'section_id=' + section_id + '&status=approved',
					success: function(response) {
						$('input[value=' + section_id + ']', _region).parents('li').remove();
						_task_list.html('');
						var new_section_id = $('input[name=section_id]:first', _region).val();
						_highlightFirstItemInQueue();
						_getTaskList(new_section_id);
						_setClassInfo(new_section_id);
					}
				});
			} else if (target.hasClass('remove-syllabus')) {
				_promptSyllabusRemoval(section_id);
				$.ajax({
					url: '/admin-syllabus-status',
					type: 'POST',
					cache: false,
					data: 'section_id=' + section_id + '&status=removed',
					success: function(response) {
						/**
						$('input[value=' + section_id + ']', _region).parents('li').remove();
						_task_list.html('');
						var new_section_id = $('input[name=section_id]:first', _region).val();
						_highlightFirstItemInQueue();
						_getTaskList(new_section_id);
						_setClassInfo(new_section_id);
						**/
					}
				});
			} else if (target.hasClass('link-section')) {
			}
		});
	};

	/**
	 * Bind task editor action to newly created task list
	 */
	var _bindTaskEditorAction = function() {
		$('.action', _task_list).delegate('a.button', 'click', function(e) {
			e.preventDefault();
			var target = $(this);
			if (target.hasClass('edit')) {
			} else if (target.hasClass('remove')) {
				$.ajax({
					url: '/admin-quest-status',
					type: 'POST',
					cache: false,
					data: 'quest_id=' + $('input[name=task_id]', target).val()+ '&status=removed',
					success: function(response) {
						if (response.success) {
							target.parents('li').hide();
						}
					}
				});

			}
		});
	};

	/**
	 * Implement Task::getTaskList()
	 *
	 * Get to-do list from server
	 */
	var _getTaskList = function(sectionId) {
		var paginate   = $('input[name=paginate]', _task_option).val();
		_task_list.addClass('loading');
		$.ajax({
			url: '/class-list-task',
			type: 'POST',
			cache: false,
			data: 'section_id=' + sectionId + '&paginate=' + paginate + '&filter=all',
			success: function(response) {
				_task_list.removeClass('loading');
				if (response.success) {

					// debug
					// console.log(response.list);
					var tasks = _task.AddUrlToTask(response.list);

					Task.generateList(tasks, _task_list);
					_bindTaskEditorAction();
				}

				if (response.error) {
					_task.setError(response.message, _task_list);
				}
			}
		});
	};


	/**
	 * Generate a queue list 
	 */
	var _generateList = function(list, region) {
		var html = '';
		var item = null;

		// if there is only one single item.
		for (i in list) {
			item = list[i];
			html += "<li><dl>";
			html += "<dt>" + item['subject_abbr'] + '-' + item['course_num'] + ' ' + item['section_num'] + "</dt>";
			html += "<dd class='action'><a href='#' class='edit button'>edit &#187;" +
				"<form name='section-info'>" +
					'<input type="hidden" name="course_id" value="' + item['course_id'] + '" />' +
					'<input type="hidden" name="section_id" value="' + item['section_id'] + '" />' +
				"</form>" +
			"</a></dd>";
			html += "</dl></li>";
		}


		if ($('li', region).length == 0) {
			html = "<div class='queue-list-inner'>" + 
				"<ul>" +
					html +
				"<ul>" + 
			"</div>";

			region.html(html);
		} else {
			$('li:last', region).after(html);
		}

	};

	/**
	 * Set class information
	 */
	var _setClassInfo = function(section_id) {
		$('input[name=section_id]', _task_option).val(section_id);
		$('input[name=section_id]', _option).val(section_id);
	}

	/**
	 * Bind action to class edit
	 */
	var _bindAction = function() {
		_region.delegate('a.edit.button', 'click', function(e) {
			var target = $(this);
			_highlightSelected(target.parents('li'));
			$.ajax({
				url: '/college-class-info',
				type: 'POST',
				cache: false,
				data: $('form', target).serialize(),
				success: function(response) {
					$('input[name=paginate]', _task_option).val(0);
					_region.removeClass('loading');
					_task_list.html('');
					_setClassInfo(response.content.section_id);
					_getTaskList(response.content.section_id);
					$('.panel-02 .more').removeClass('disabled');
				}
			});
		});
	};

	/**
	 * Reset syllabus sub-menu
	 */
	this.resetMenu = function() {
		$('input[name=status]', _option).val(_status.new);
	};

	/**
	 * Set class info
	 */
	this.setInfo = function(section_id) {
		_setClassInfo(section_id);
	}

	/**
	 * Get a list of classes with syllabus 
	 */
	this.updateList = function() {
		_region.addClass('loading');
		$.ajax({
			url: '/college-class-list',
			type: 'POST',
			cache: false,
			data: _option.serialize(),
			success: function(response) {
				_region.removeClass('loading');
				_generateList(response.list, _region);
				_bindAction();
				_getMoreTask();
			}
		});
	};

	/**
	 * Get a list of classes with syllabus 
	 */
	this.getList = function() {
		_region.addClass('loading');
		$.ajax({
			url: '/college-class-list',
			type: 'POST',
			cache: false,
			data: _option.serialize(),
			success: function(response) {
				_region.removeClass('loading');
				_generateList(response.list, _region);
				_bindAction();
				_bindSyllabusAdminAction();
				var section_id = $('input[name=section_id]:first', _region).val();
				_getTaskList(section_id);
				_setClassInfo(section_id);
				_highlightFirstItemInQueue();
			}
		});
	};
};

/**
 * Override Task::generateList()
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
			html += "<dd class='objective'>" + item['objective'] + "</dd>";
		} else {
			html += "<dt>" + item['objective'] + "</dt>";
		}

		var task_info = "<form name='section-info'>" +
				'<input type="hidden" name="section_id" value="' + item['section_id'] + '" />' +
				'<input type="hidden" name="task_id" value="' + item['id'] + '" />' +
			"</form>";

		html += "<dd class='action'>" +
			"<a href='#' class='remove button'>remove &#187;" +
				task_info +
			"</a>" +
			"<a href='#' class='edit button'>edit &#187;" +
				task_info +
			"</a>" +
		"</dd>";

		html += "<dd id='" + item['due_date'] + "' class='due_date count-down'>" + item['due_date'] + "</dd>";
		html += item['location'] != null ? "<dd class='location'>" + item['location'] + "</dd>" : "";
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

};
