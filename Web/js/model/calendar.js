/**
 * @file
 * Create calendar of events
 *
 * @see js/model/taskjs
 */
window.Calendar = function(regionName, optionFormName, listName, creationFormName) {

	var _taskUpdater = '';

	/**
	 * HTML region to be populated with the calendar
	 */
	var region = $(regionName);

	/**
	 * HTML Form to store configuration
	 */
	var option = $(optionFormName);

	/**
	 * HTML region to be populated with events
	 */
	var list = $(listName);

	/**
	 * Store generated HTML for future use
	 */
	var cache = new Cache();

	/**
	 * Type of calendar
	 */
	var type = $('input[name=type]', option).val();

	/**
	 * Hour interval
	 */
	var hourInterval = 4;

	/**
	 * current timestamp
	 */
	var timestamp = $('input[name=timestamp]', option).val();

	/**
	 * Inherit from Task
	 */
	var task = new Task(creationFormName, optionFormName);

	/**
	 * Time range which the calendar covers
	 *
	 * This is a helper/private method.
	 */
	var range = {
		'begin' : $('input[name=begin]', option).val(),
		'end'   : $('input[name=end]', option).val()
	};

	/**
	 * private attributes
	 */
	var weekArray  = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var monthArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	/**
	 * Get event list from server
	 *
	 * It also clears out timeslots before re-populating.
	 *
	 * This a private function because javascript is retarded. By default the 
	 * 'this' keyword refer to the current function, thus making it impossible 
	 *  to call class methods within a definition to share code.
	 */
	var getTaskList = function() {
		var paginate   = $('input[name=paginate]', option).val();
		$('.has-event').removeClass('has-event');
		list.empty();

		/**
		 * A helper function to populate calendar timeslot
		 */
		var populateTimeSlot = function(itemList) {

			if (itemList && itemList['id']) {
				findTimeInterval(itemList);
			} else if (itemList) {
				for (i in itemList) {
					findTimeInterval(itemList[i]);

				}
			}
			// auto scroll to the first task on day calendars
			if (type.indexOf('day') > 0) {
				var offset = $('.has-event:first', region).offset();
				if (offset) {
					var newTop = offset.top - $('.has-event:first', region).height();
					$('.scrollable-inner').animate({scrollTop: newTop}, 'slow');
				}
			}
		}

		list.addClass('loading');
		$('span.event').remove();
		$.ajax({
			url: '/calendar-list-task',
			type: 'POST',
			cache: false,
			data: option.serialize(),
			success: function(response) {
				list.removeClass('loading');
				if (response.success) {
					var tasks = task.AddUrlToTask(response.list);

					Task.generateList(tasks, list);
					populateTimeSlot(tasks);

					_taskUpdater = new TaskUpdater(listName);
					
				}

				if (response.error) {
					task.setError(response.message, list);
				}
			}
		});
	}

	/**
	 * A hash function to map a event to a timeslot on the calendar.
	 *
	 * This is a helper/private method.
	 */
	var findTimeInterval = function(item) {
		var date  = new Date(item['due_date'] * 1000);
		var begin = '';
		var end   = '';

		// debug
		// console.log(date);

		date.setMinutes(0, 0, 0);

		if (type.indexOf('day') > 0) {
			date.setHours(date.getHours());
			begin = toTimestamp(date);
			date.setHours(date.getHours() + 1 * hourInterval);
			end   = toTimestamp(date);

		} else if (type == 'month') {
			date.setHours(0, 0, 0, 0);
			begin = toTimestamp(date) ;
			date.setTime(date.getTime() + 86400000);
			end   = toTimestamp(date);
		}

		// debug
		// console.log(begin);
		// console.log(end);

		// JQuery is having trouble selecting newly created elements, that's why
		var timeslot = $(document.getElementById(begin + '.' + end), region);
		if (item['subject_abbr'] && item['course_num']) {
			timeslot.html(timeslot.html() + wrap(item['subject_abbr'] + '-' + item['course_num']));
		}
		timeslot.addClass('has-event');
	};

	/**
	 * Wrap event in a <span>
	 *
	 * This is a helper/private method.
	 */
	var wrap = function(text) {
		return '<span class="event">' + text + '</span>';
	}

	/**
	 * Save range to calendar option
	 *
	 * This is a helper/private method.
	 */
	var setCalendarRange = function() {
		$('input[name=begin]', option).val(range.begin);
		$('input[name=end]', option).val(range.end);
	};

	/**
	 * Save type to calendar option
	 *
	 * This is a helper/private method.
	 */
	var setCalendarType = function() {
		$('input[name=type]', option).val(type);
	}

	/**
	 * Convert a data object to UNIX timestamp
	 *
	 * This is a helper/private method.
	 *
	 * @param object date
	 *  JavaScript Date object
	 * 
	 * @return string
	 *  UNIX timestamp
	 */
	var toTimestamp = function(date) {
		return Math.round(date.getTime() / 1000) - (date.getTimezoneOffset() * 60);
	};

	/**
	 * Display calendar in day interval
	 *
	 * This is a helper/private method.
	 */
	var displayDay = function() {
		var date = new Date();
		var currentMonth = '';
		var currentDate  = '';

		var html = '<div class="day calendar-display-inner">' +
			'<div class="col hour-interval">';

		for (i = 0; i < 24 / hourInterval; i++) {
			notation = ((i * hourInterval) < 12) ? ' am' : ' pm'; 
			rowType  = (i % 2 == 0) ? 'even' : 'odd';
			html += '<div class="row">' +
				'<span class="hour-notation">' + (i * hourInterval)+ notation + '</span>' +
			'</div>';
		}

		html += '</div>';

		for (i = range.begin; i < range.end; i += 86400) {
			date.setTime((i + date.getTimezoneOffset() * 60) * 1000);
			currentMonth = date.getMonth() + 1;
			currentDate  = date.getDate();
			html += '<div class="col day-interval"><div class="label row">' +
				currentMonth + '/' + currentDate +
			'</div>';
			var offset = 3600 * hourInterval;
			for (j = i - offset; j < i + 86400; j += offset) {
				rowType = ((j / offset) % 2 == 0) ? 'even' : 'odd';
				html += '<div id="'+ j + '.' + (j + offset) + '" class="row ' + rowType + ' event"></div>';
			}
			html += '</div>';
		}

		html += '</div></div>';

		region.html(html);

		// debug
		// console.log(region);

		// adjust row width according to content
		var rowWidth = (region.width()  - 58)/ ((range.end - range.begin) / 86400);
		$('.day-interval .row', region).width(rowWidth);
		var row = $('.row:not(.label)', region);
		var rowHeight = (row.height() * hourInterval);
		row.height(rowHeight);
	};

	/**
	 * Display calendar in month interval
	 *
	 * This is a helper/private method.
	 */
	var displayMonth = function() {
		var html = '<div class="month calendar-display-inner">' + 
			'<div class="month-label">' +
			monthArray[((new Date(range.begin * 1000)).getMonth() + 1)] +
			'</div>' +
		'<div class="week-day-interval row">';

		// create week day labels
		for (var i = 0; i < 7; i++) {
			html += '<div class="label col">' + weekArray[i] + '</div>';
		}

		html += '</div><div class="week row">';

		var date = new Date(range.begin * 1000);
		var firstDayInWeek = date.getDay() +1;
		// create padding between the first day of the week and the beginning of 
		// the month
		var colType = '';
		for (j = 0; j < firstDayInWeek; j++) {
			colType = (j % 2 == 0) ? 'even' : 'odd';
			html += '<div class="day col ' + colType + '"></div>';
		}

		// cycle through the days
		for (k = range.begin; k <= range.end; k += 86400) {
			curDate = new Date((k + (new Date()).getTimezoneOffset() * 60) * 1000);
			colType = (curDate.getDate() % 2 == 0) ? 'even' : 'odd';
			html += '<div id="' + k + '.' + (k + 86400) + '" class="day col ' + colType + '">' + 
				'<span class="day-label">' + curDate.getDate() + '</span>' + 
			'</div>';
			html += curDate.getDay() == 6 ? '</div><div class="week row">' : '';
		}

		html += '</div>' +
			'</div>';

		region.html(html);

		// adjust row width according to content
		var rowWidth = ($('.calendar-display').width() - 15)/ 7;
		$('.calendar-display .month .col').width(rowWidth);
	};

	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given day range
	 *
	 * This is a helper/private method.
	 *
	 * @param string timestamp
	 *  UNIX timestamp of the beginning date
	 * @param string number
	 *  number of days
	 *
	 * @return object
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	var calculateDayRange = function(number) {
		var date = new Date(timestamp * 1000);
		date.setHours(0, 0, 0, 0);
		range.begin = toTimestamp(date);
		date.setDate(date.getDate() + number);
		range.end = toTimestamp(date);

		// debug
		// console.log('timestamp');
		// console.log(timestamp);
	};

	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given week range
	 *
	 * This is a helper/private method.
	 *
	 * @return object
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	var calculateWeekRange = function() {
		var date = new Date(timestamp * 1000);
		date.setHours(0, 0, 0, 0);
		date.setTime(date.getTime() - ((date.getDay() * 86400) * 1000));
		range.begin = toTimestamp(date);
		date.setTime(date.getTime() + 6 *86400 * 1000);
		range.end = toTimestamp(date);
		return range;
	};

	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given moneth range
	 *
	 * This is a helper/private method.
	 *
	 * @return object
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	var calculateMonthRange = function() {
		var date = new Date(timestamp * 1000);
		date.setDate(1)
		date.setHours(0, 0, 0, 0);
		range.begin = toTimestamp(date);

		// we wrap the month around to 1 if it's the end of the year and increment 
		// year by 1
		if (date.getMonth() == 11) {
			date.setMonth(1);
			date.setFullYear(date.getFullYear() +1);
		} else {
			date.setMonth(date.getMonth() + 1);
		}

		range.end  = toTimestamp(new Date(date.getFullYear(), date.getMonth(), 0));
	};

	/**
	 * Get the calendar using day as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 * @param string number
	 *  number of days
	 *
	 * @return object range
	 */
	this.getDayCalendar = function(number) {
		calculateDayRange(number);
		type  = number + '-day';
		displayDay();
		setCalendarRange();
		setCalendarType();
	};

	/**
	 * Get the calendar using week as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 *
	 * @return object range
	 */
	this.getWeekCalendar = function() {
		calculateWeekRange();
		type  = '7-day';
		displayDay();
		setCalendarRange();
		setCalendarType();
	};

	/**
	 * Get the calendar using month as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 *
	 * @return object range
	 */
	this.getMonthCalendar = function() {
		calculateMonthRange();
		type  = 'month';
		displayMonth();
		setCalendarRange();
		setCalendarType();
	};

	/**
	 * Extend Task::createTask()
	 *
	 * Create task as calendar event
	 */
	this.createTask = function() {
		cache.unset('task-list-' + range.begin + '-' + range.end);
		list.empty();
		task.createTask(getTaskList);
	};

	/**
	 * Populate task list and calendar
	 */
	this.populate = function() {
		getTaskList();
	};

	/**
	 * Extend Task::incrementPaginate().
	 */
	this.incrementPaginate = function() {
		task.incrementPaginate();
	}
};
