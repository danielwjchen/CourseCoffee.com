/**
 * @file
 * Create calendar of events
 *
 * @see js/model/taskjs
 */
window.Calendar = function(regionName, optionName, listName, creationFormName) {

	/**
	 * HTML region to be populated with the calendar
	 */
	var region = $(regionName);

	/**
	 * HTML Form to store configuration
	 */
	var option = $(optionName);

	/**
	 * HTML region to be populated with events
	 */
	var list = $(listName);

	/**
	 * Store generated HTML for future use
	 */
	var cache = {};

	/**
	 * Type of calendar
	 */
	var type = $('input[name=type]', option).val();

	/**
	 * current timestamp
	 */
	var timestamp = $('input[name=timestamp]', option).val();

	/**
	 * Inherit from Task
	 */
	var task = new Task(creationFormName);

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
	var monthArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dev'];

	/**
	 * Get event list from server
	 *
	 * It also clears out timeslots before re-populating.
	 *
	 * This a private function because javascript is retarded. By default the 
	 * 'this' keyword refer to the current function, thus making it impossible 
	 *  to call class methods within a definition to share code.
	 */
	var getEventList = function() {
		list.addClass('loading');

		$.ajax({
			url: '/calendar-list-task',
			type: 'POST',
			cache: false,
			data: option.serialize(),
			success: function(response) {
				list.removeClass('loading');
				if (response.success) {

					// debug
					// console.log(response.list);

					Task.generateList(response.list, list);
					if (response.list && response.list['id']) {
						findTimeInterval(response.list);
					} else if (response.list) {
						for (i in response.list) {
							findTimeInterval(response.list[i]);

						}
					}
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
		date = new Date(item['due_date'] * 1000);
		date.setMinutes(0, 0, 0);

		if (type == 'day') {
			begin = date.getTime() / 1000;
			end   = (date.setHours(date.getHours() + 1))/ 1000;

		} else if (type == 'month') {
			date.setHours(0, 0, 0, 0);
			begin = date.getTime() / 1000;
			end   = (date.setTime(date.getTime() + 86400000))/ 1000;
		}

		// JQuery is having trouble selecting newly created elements, that's why
		timeslot = $(document.getElementById(begin + '.' + end), region);
		timeslot.html('');
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
		return Math.round(date.getTime() / 1000);
	};

	/**
	 * Display calendar in day interval
	 *
	 * This is a helper/private method.
	 */
	var displayDay = function() {
		date = new Date();
		currentMonth = '';
		currentDate  = '';

		html = '<div class="day calendar-display-inner">' +
			'<div class="col hour-interval">';

		for (i = 0; i < 24; i++) {
			notation = (i < 12) ? ' am' : ' pm'; 
			rowType  = (i % 2 == 0) ? 'even' : 'odd';
			html += '<div class="row">' +
				'<span class="hour-notation">' + i + notation + '</span>' +
			'</div>';
		}

		html += '</div>';

		for (i = range.begin; i < range.end; i += 86400) {
			date.setTime(i * 1000);
			currentMonth = date.getMonth() + 1;
			currentDate  = date.getDate();
			html += '<div class="col day-interval"><div class="label row">' +
				currentMonth + '/' + currentDate +
			'</div>';
			for (j = i; j < i + 86400; j += 3600) {
				rowType = ((j / 3600) % 2 == 0) ? 'even' : 'odd';
				html += '<div id="'+ j + '.' + (j + 3600) + '" class="row ' + rowType + ' event"></div>';
			}
			html += '</div>';
		}

		html += '</div></div>';

		region.html(html);

		// debug
		// console.log(region);

		// adjust row width according to content
		var rowWidth = (region.width()  - 38)/ ((range.end - range.begin) / 86400);
		$('.day-interval .row', region).width(rowWidth);
	};

	/**
	 * Display calendar in month interval
	 *
	 * This is a helper/private method.
	 */
	var displayMonth = function() {
		html = '<div class="month calendar-display-inner">' + 
			'<div class="month-label">' +
			monthArray[((new Date(range.begin * 1000)).getMonth())] +
			'</div>' +
		'<div class="week-day-interval row">';

		// create week day labels
		for (var i = 0; i < 7; i++) {
			html += '<div class="label col">' + weekArray[i] + '</div>';
		}

		html += '</div><div class="week row">';

		firstDayInWeek = (new Date(range.begin * 1000)).getDay();
		// create padding between the first day of the week and the beginning of 
		// the month
		for (j = 0; j < firstDayInWeek; j++) {
			colType = (j % 2 == 0) ? 'even' : 'odd';
			html += '<div class="day col ' + colType + '"></div>';
		}

		// cycle through the days
		for (k = range.begin; k <= range.end; k += 86400) {
			curDate = new Date(k * 1000);
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
		date = new Date(timestamp * 1000);
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
		date = new Date(timestamp * 1000);
		date.setHours(0, 0, 0, 0);
		date.setTime(date.getTime() - ((date.getDay() * 86400) * 1000));
		range.begin = toTimestamp(date);
		date.setTime(date.getTime() + 7 *86400 * 1000);
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
		date = new Date(timestamp * 1000);
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
		type  = 'day';
		displayDay();
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
		type  = 'day';
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
	 * Create task as calendar event
	 */
	this.createTask = function(creationFormName) {
		task.createTask(getEventList);
	};

	/**
	 * Populate task list and calendar
	 */
	this.populate = function() {
		getEventList();
	};
};
