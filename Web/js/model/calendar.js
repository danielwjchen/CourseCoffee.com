/**
 * @file
 * Create calendar of events
 */
window.calendar = {
	'init' : function(type, timestamp) {
		switch (type) {
			case 'today':
				calendar.getDayCalendar(timestamp, 1);
				break;
		}
	},
	/**
	 * Get the calendar using day as an interval
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 * @param string number
	 *  number of days
	 */
	'getDayCalendar' : function(timestamp, number) {
		range = calendar.calculateDayRange(timestamp, number);
		data = calendar.update();
		calendar.displayDay(range, data);
	},
	/**
	 * Get the calendar for a month
	 *
	 * @param string timestamp
	 *  UNIX timestamp
	 */
	'getMonth' : function(timestamp) {
		range = calendar.calculateMonthRange(timestamp);
		data = calendar.update();
		calendar.displayMonth(range, data);
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
	'update' : function() {
	},
	/**
	 * Display calendar for multiple days
	 *
	 * @param object range
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 * @param object data
	 *  a list of events to be listed on the calendar
	 */
	'displayDay': function(range, data) {
		var date = new Date();
		var currentMonth = '';
		var currentDate  = '';
		var html = '<div class="calendar-display-inner">' +
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
				html += '<div class="row ' + rowType + ' event"></div>';
			}
			html += '</div>';
		}
		html += '</div>' + 
		'</div>';
		$('.calendar-display').html(html);

		// adjust row width according to content
		var rowWidth = ($('.calendar-display').width()  - 45)/ ((range.end - range.begin) / 86400);
		$('.calendar-display .day-interval .row').width(rowWidth);
	},
	/**
	 * Display calendar for multiple months
	 *
	 * @param object range
	 *  an object with two UNIX timestamps as attributes
	 *   - begin
	 *   - end
	 * @param object data
	 *  a list of events to be listed on the calendar
	 */
	'displayMonth' : function(range, data) {
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
			html += '<div class="day col ' + colType + '"></div>';
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
