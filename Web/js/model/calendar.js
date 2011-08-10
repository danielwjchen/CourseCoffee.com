/**
 * @file
 * Create calendar of events
 *
 * @see js/model/taskjs
 */
window.calendar = {
	'init' : function(type, timestamp) {

		/**
		 * a helper/private function to save range to calendar option
		 *
		 * @param object range
		 */
		saveCalendarRange = function(range) {
			$('input[name=begin]', calendar.option).val(range.begin);
			$('input[name=end]', calendar.option).val(range.end);
		};

		switch (type) {
			case 'this month':
				calendar.range = calendar.getMonthCalendar(timestamp);
				calendar.type  = 'month';
				break;
			case 'this week':
				calendar.range = calendar.getDayCalendar(timestamp, 7);
				calendar.type  = 'day';
				break;
			default:
			case 'today':
				calendar.range = calendar.getDayCalendar(timestamp, 1);
				calendar.type  = 'day';
				break;
		}
		calendar.option = $('#calendar-option-menu');
		saveCalendarRange(calendar.range);
		calendar.taskPanel = $('.panel-02 .panel-inner .task-list');
		calendar.populate();
	},
	/**
	 * Set the calendar options
	 */
	'setCalendarOption' : function(range) {
		$('input[name=begin]', calendar.option).val(range.begin);
		$('input[name=end]', calendar.option).val(range.end);
	},
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
	'getDayCalendar' : function(timestamp, number) {
		range = calendar.calculateDayRange(timestamp, number);
		calendar.type  = 'day';
		calendar.displayDay(range);
		return range;
	},
	/**
	 * Get the calendar using week as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 *
	 * @return object range
	 */
	'getWeekCalendar' : function(timestamp) {
		range = calendar.calculateWeekRange(timestamp);
		calendar.type  = 'day';
		calendar.displayDay(range);
		return range;
	},
	/**
	 * Get the calendar using month as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 *
	 * @return object range
	 */
	'getMonthCalendar' : function(timestamp) {
		range = calendar.calculateMonthRange(timestamp);
		calendar.type  = 'month';
		calendar.displayMonth(range);
		return range;
	},
	/**
	 * Convert a data object to UNIX timestamp
	 *
	 * @param object date
	 *  JavaScript Date object
	 * 
	 * @return string
	 *  UNIX timestamp
	 */
	'toTimestamp' : function(date) {
		return Math.round(date.getTime() / 1000);
	},
	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given day range
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
	'calculateDayRange' : function(timestamp, number) {
		var date  = new Date(timestamp * 1000);
		var range = {};
		date.setHours(0, 0, 0, 0);
		range.begin = calendar.toTimestamp(date);
		date.setDate(date.getDate() + number);
		range.end = calendar.toTimestamp(date);
		return range;
	},
	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given week range
	 *
	 * @param string timestamp
	 *  UNIX timestamp of the beginning date
	 *
	 * @return object
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	'calculateWeekRange' : function(timestamp) {
		var date  = new Date(timestamp * 1000);
		var range = {};
		date.setHours(0, 0, 0, 0);
		date.setTime(date.getTime() - ((date.getDay() * 86400) * 1000));
		range.begin = calendar.toTimestamp(date);
		date.setTime(date.getTime() + 7 *86400 * 1000);
		range.end = calendar.toTimestamp(date);
		return range;
	},
	/**
	 * Caluculate the beginning and ending UNIX timestamps for a given moneth range
	 *
	 * @param string timestamp
	 *  UNIX timestamp of the beginning date
	 *
	 * @return object
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	'calculateMonthRange' : function(timestamp) {
		var date = new Date(timestamp * 1000);
		var range = {};
		date.setDate(1)
		date.setHours(0, 0, 0, 0);
		range.begin = calendar.toTimestamp(date);

		// we wrap the month around to 1 if it's the end of the year and increment 
		// year by 1
		if (date.getMonth() == 11) {
			date.setMonth(1);
			date.setFullYear(date.getFullYear() +1);
		} else {
			date.setMonth(date.getMonth() + 1);
		}

		range.end  = calendar.toTimestamp(new Date(date.getFullYear(), date.getMonth(), 0));
		return range;
	},
	/**
	 * Populate task list and calendar
	 *
	 * It also clears out the timeslot before populating it.
	 */
	'populate' : function() {
		calendar.taskPanel.addClass('loading');
		/**
		 * Wrap event in a <span>
		 */
		wrap = function(text) {
			return '<span class="event">' + text + '</span>';
		}
		/**
		 * A hash function to map a event to a timeslot on the calendar.
		 */
		findTimeInterval = function(item) {
			date = new Date(item['due_date'] * 1000);
			date.setMinutes(0, 0, 0);
			if (calendar.type == 'day') {
				begin = date.getTime() / 1000;
				end = (date.setHours(date.getHours() + 1))/ 1000;
			} else if (calendar.type == 'month') {
				date.setHours(0, 0, 0, 0);
				begin = date.getTime() / 1000;
				end = (date.setTime(date.getTime() + 86400000))/ 1000;
			}
			// JQuery is having trouble selecting newly created elements, that's why
			timeslot = $(document.getElementById(begin + '.' + end), $('.calendar-display'));
			timeslot.html('');
			timeslot.html(timeslot.html() + wrap(item['objective']));
			timeslot.addClass('has-event');
		};

		$.ajax({
			url: '?q=calendar-list-task',
			type: 'POST',
			cache: false,
			data: calendar.option.serialize(),
			success: function(response) {
				calendar.taskPanel.removeClass('loading');
				if (response.success) {
					task.generateList(response.list, calendar.taskPanel);
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
	},
	/**
	 * Display calendar in day interval
	 *
	 * @param object range
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	'displayDay': function(range) {
		var date = new Date();
		var currentMonth = '';
		var currentDate  = '';
		var html = '<div class="day calendar-display-inner">' +
			'<div class="col hour-interval">';
		for (i = 0; i < 24; i++) {
			notation = (i < 12) ? ' am' : ' pm'; 
			rowType  = (i % 2 == 0) ? 'even' : 'odd';
			html += '<div class="row">' +
				'<span class="hour-notation">' + i + notation + '</span>' +
			'</div>';
		}
		html += '</div>';
		for (var i = range.begin; i < range.end; i += 86400) {
			date.setTime(i * 1000);
			currentMonth = date.getMonth() + 1;
			currentDate  = date.getDate();
			html += '<div class="col day-interval"><div class="label row">' +
				currentMonth + '/' + currentDate +
			'</div>';
			for (var j = i; j < i + 86400; j += 3600) {
				rowType = ((j / 3600) % 2 == 0) ? 'even' : 'odd';
				html += '<div id="'+ j + '.' + (j + 3600) + '" class="row ' + rowType + ' event"></div>';
			}
			html += '</div>';
		}
		html += '</div>' + 
		'</div>';
		$('.calendar-display').html(html);

		// adjust row width according to content
		var rowWidth = ($('.calendar-display').width()  - 38)/ ((range.end - range.begin) / 86400);
		$('.calendar-display .day-interval .row').width(rowWidth);
	},
	/**
	 * Display calendar in month interval
	 *
	 * @param object range
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 */
	'displayMonth' : function(range) {
		var weekArray  = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
		var monthArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dev'];
		var html = '<div class="month calendar-display-inner">' + 
			'<div class="month-label">' +
			monthArray[((new Date(range.begin * 1000)).getMonth())] +
			'</div>' +
		'<div class="week-day-interval row">';

		// create week day labels
		for (var i = 0; i < 7; i++) {
			html += '<div class="label col">' + weekArray[i] + '</div>';
		}

		html += '</div><div class="week row">';

		var firstDayInWeek = (new Date(range.begin * 1000)).getDay();
		// create padding between the first day of the week and the beginning of 
		// the month
		for (var j = 0; j < firstDayInWeek; j++) {
			colType = (j % 2 == 0) ? 'even' : 'odd';
			html += '<div class="day col ' + colType + '"></div>';
		}

		// cycle through the days
		for (var k = range.begin; k <= range.end; k += 86400) {
			curDate = new Date(k * 1000);
			colType = (curDate.getDate() % 2 == 0) ? 'even' : 'odd';
			html += '<div id="' + k + '.' + (k + 86400) + '" class="day col ' + colType + '">' + 
				'<span class="day-label">' + curDate.getDate() + '</span>' + 
			'</div>';
			html += curDate.getDay() == 6 ? '</div><div class="week row">' : '';
		}

		html += '</div>' +
			'</div>';

		$('.calendar-display').html(html);

		// adjust row width according to content
		var rowWidth = ($('.calendar-display').width() - 15)/ 7;
		$('.calendar-display .month .col').width(rowWidth);
	}

};
