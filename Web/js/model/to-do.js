/**
 * @file
 * Manage user tasks
 *
 * @see js/model/task.js
 */
window.ToDo = function(optionFormName, listName, creationFormName) {

	/**
	 * HTML Form to store configuration
	 */
	var option = $(optionFormName);

	/**
	 * HTML region to be populated with to-do list
	 */
	var list = $(listName);

	/**
	 * Store generated HTML for future use
	 */
	var cache = new Cache();

	/**
	 * Inherit from Task
	 */
	var task = new Task(creationFormName, optionFormName);

	var _taskUpdater = '';

	/**
	 * Implement Task::getTaskList()
	 *
	 * Get to-do list from server
	 */
	var getTaskList = function() {
		list.addClass('loading');
		$.ajax({
			url: '/user-list-task',
			type: 'POST',
			cache: false,
			data: option.serialize(),
			success: function(response) {
				list.removeClass('loading');
				if (response.success) {

					// debug
					// console.log(response.list);
					tasks = task.AddUrlToTask(response.list);

					Task.generateList(response.list, list);
					_taskUpdater = new TaskUpdater(listName);
				}

				if (response.error) {
					task.setError(response.message, list);
				}
			}
		});
	};

	/**
	 * Implement Task::createTask()
	 *
	 * Create task as user's to-do
	 */
	this.createTask = function() {
		// we flush all cache because it does not make sense to have cache paginated
		// result. It's easier to rebuild the list all together
		cache.flush();
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
