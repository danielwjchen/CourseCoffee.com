/**
 * @file
 * Handle access to class information and related assignments
 *
 * @see js/model/task.js
 */
window.ClassInfo = function(regionName, optionFormName, listName, creationFormName) {
	
	var region = $(regionName);
	
	var option = $(optionFormName);

	/**
	 * HTML region to be populated with to-do list
	 */
	var list = $(listName);

	/**
	 * Store generated HTML for future use
	 */
	var cache  = new Cache();

	/**
	 * Inherit from Task
	 */
	var task = new Task(creationFormName, optionFormName);

	/**
	 * Process default class data if specified
	 */
	var defaultData = option.serializeArray();
	
	if (defaultData.length != 0) {
		data = {};
		for (i in defaultData) {
			key = defaultData[i].name.replace('-', '_');
			data[key] = defaultData[i].value;
		}

		cache.set(data['section_id'], data);
	}

	/**
	 * Implement Task::getTaskList()
	 *
	 * Get to-do list from server
	 */
	var getTaskList = function() {
		var sectionId  = $('input[name=section-id]', option).val();
		var cacheKey   = 'task-list-' + sectionId;
		var cacheValue = cache.get(cacheKey);

		if (cacheValue) {
			// debug
			console.log('getTaskList cache');
			console.log(cacheValue);
			Task.generateList(cacheValue, list);
		} else {
			list.addClass('loading');

			// debug
			// console.log(option);

			$.ajax({
				url: '/class-list-task',
				type: 'POST',
				cache: false,
				data: 'section_id=' + sectionId,
				success: function(response) {
					list.removeClass('loading');
					if (response.success) {

						// debug
						// console.log(response.list);

						cache.set(cacheKey, response.list);
						Task.generateList(response.list, list);
					}
				}
			});
		}
	};

	/**
	 * Set option values to the data recieved
	 *
	 * @param object data
	 */
	var setClassOption = function(data) {
		$('input[name=institution-id]', option).val(data.institution_id);
		$('input[name=institution-uri]', option).val(data.institution_uri);
		$('input[name=institution]', option).val(data.institution);
		$('input[name=year-id]', option).val(data.year_id);
		$('input[name=year]', option).val(data.year);
		$('input[name=term]', option).val(data.term_id);
		$('input[name=term-id]', option).val(data.term);
		$('input[name=subject-id]', option).val(data.subject_id);
		$('input[name=subject-abbr]', option).val(data.subject_abbr);
		$('input[name=course-id]', option).val(data.course_id);
		$('input[name=course-title]', option).val(data.course_title);
		$('input[name=course-description]', option).val(data.course_description);
		$('input[name=course-num]', option).val(data.course_num);
		$('input[name=section-id]', option).val(data.section_id);
		$('input[name=section-num]', option).val(data.section_num);
	};
	
	/**
	 * Display class information stored in option
	 */
	var displayClassInfo = function() {
		var content = '<h3 class="course-title">' + $('input[name=course-title]', option).val() + '</h3>';
		var courseInfo = $('input[name=course-description]', option).val();
		content += courseInfo != undefined ? '<p>' + courseInfo + '</p>' : '';
		region.html(content);
	}

	/**
	 * Get information for a class from server
	 *
	 * Note that this also gets the list of assigments from that class!
	 *
	 * @param int sectionId
	 */
	this.getClassInfo = function(sectionId) {
		list.empty();
		region.empty();
		var cacheKey = 'class-info-' + sectionId;
		var cacheValue = cache.get(cacheKey);
		if (cacheValue) {
			// debug
			// console.log(cache);

			setClassOption(cacheValue);
			displayClassInfo();
			getTaskList();

		} else {
			region.addClass('loading');
			$.ajax({
				url: '/college-class-info',
				type: 'post',
				cache: true,
				data: 'section_id=' + sectionId,
				success: function(response) {
					region.removeClass('loading');
					if (response.content) {
						cache.set(cacheKey, response.content);
						setClassOption(response.content);
						displayClassInfo();
						getTaskList();
					}
				}
			});
		}
	}

	/**
	 * Get the section id of the class storeed in option
	 */
	this.getClassId = function() {
		return $('input[name=section-id]', option).val();
	}

	/**
	 * Implement Task::createTask()
	 *
	 * Create task as user's to-do
	 */
	this.createTask = function() {
		cache.unset('task-list-' + $('input[name=section-id]', option).val());
		list.empty();
		task.createTask(getTaskList);
	};

	/**
	 * Populate to-do list
	 */
	this.populate = function() {
		getTaskList();
	};

	/**
	 * Extend Task::incrementPaginate().
	 */
	this.incrementPaginate = function() {
		task.incrementPaginate();
	};
}
