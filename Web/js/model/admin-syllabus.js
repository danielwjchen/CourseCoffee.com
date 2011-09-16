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
		_creationForm.html() +
		'<form name="class-option" id="class-option-form">' +
			'<input type="hidden" name="section_id" />' +
			'<input type="hidden" name="paginate" value="0" />' +
		'</form>' +
		'<div id="syllabus-task-list" class="task-list"></div>' +
		'<a href="#" class="button more">more</a>');

	$('.panel-02 .button.more').click(function(e) {
		e.preventDefault();
		_getTaskList($('input[name=section_id]', _option).val());
	});

	var _list = $('.task-list');
	var _task = new Task('#class-task-creation-form', '#class-option-form');
	var _task_option = $('#class-option-form');
	var _status = {
		'new'      : 'has_syllabus',
		'approved' : 'approved',
		'removed'  : 'removed',
		'all'      : 'all',
	}

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

	/**
	 * Implement Task::getTaskList()
	 *
	 * Get to-do list from server
	 */
	var _getTaskList = function(sectionId) {
		var paginate   = $('input[name=paginate]', _task_option).val();
		_list.addClass('loading');
		$.ajax({
			url: '/class-list-task',
			type: 'POST',
			cache: false,
			data: 'section_id=' + sectionId + '&paginate=' + paginate + '&filter=all',
			success: function(response) {
				_list.removeClass('loading');
				if (response.success) {

					// debug
					// console.log(response.list);
					var tasks = _task.AddUrlToTask(response.list);

					Task.generateList(tasks, _list);
				}

				if (response.error) {
					_task.setError(response.message, _list);
				}
			}
		});
	};


	/**
	 * Generate a list 
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
	 * Reset syllabus sub-menu
	 */
	this.resetMenu = function() {
		$('input[name=status]', _option).val(_status.new);
	};

	/**
	 * Get detail of the class
	 */
	this.getDetail = function() {
	};

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
					_region.removeClass('loading');
					$('input[name=section_id]', _task_option).val(response.content.section_id);
					$('input[name=section_id]', _option).val(response.content.section_id);
					_list.html('');
					_getTaskList(response.content.section_id);
				}
			});
		});
	};

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
				_getTaskList($('input[name=section_id]:first', _region).val());
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
			html += "<dd>" + item['objective'] + "</dd>";
		} else {
			html += "<dt>" + item['objective'] + "</dt>";
		}

		var section_info = "<form name='section-info'>" +
				'<input type="hidden" name="section_id" value="' + item['section_id'] + '" />' +
			"</form>";

		html += "<dd class='action'>" +
			"<a href='#' class='edit button'>remove &#187;" +
				section_info +
			"</a>" +
			"<a href='#' class='edit button'>edit &#187;" +
				section_info +
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

	var commentPanel = new CommentPanel();

};
