/**
 * @file
 * Translate timestamp into more human readable and useful format
 */
$.fn.translateTime = function() {
	/**
	 * Calculate offset count
	 */
	calCount = function(offset, timeDiff) {
		return Math.floor(timeDiff / offset);
	}
	/**
	 * Calculate timestamp into the date offset time
	 */
	calOffset = function(offsetCount, offsetName) {
		if (offsetCount > 0) {
			offsetName = offsetCount > 1 ? offsetName+'s ' : offsetName+' ';
			return offsetCount + ' ' + offsetName;
		}else{
			return '';
		}
	}
	/**
	 * Set the message
	 *
	 * @param string
	 */
	setMessage = function(region, string) {
		currTimestamp  = Math.floor(currentDate.getTime() / 1000);
		dueTimestamp = parseInt($(this).attr('id'));
		message = dueTimestamp >= currTimestamp ? 'due in %s ' : 'was due %s ago';
		message = message.replace(/%s\s/, string);
		$(region).text(message);
	}

	// time offsets
	var yearOffset   = 365*24*60*60;
	var monthOffset  = 30*24*60*60;
	var weekOffset   = 7*24*60*60;
	var dayOffset    = 24*60*60;
	var hourOffset   = 60*60;
	var minuteOffset = 60;

	var currentDate = new Date()
	var currTimestamp  = Math.floor(currentDate.getTime() / 1000);
	var dueTimestamp = parseInt($(this).attr('id'));
	var timeDiff = Math.abs(dueTimestamp - currTimestamp);
	var string = '';

	var offsetCount = 0;
	offsetCount = calCount(yearOffset, timeDiff);
	string = string + calOffset(offsetCount, 'year');
	timeDiff = timeDiff  - offsetCount * yearOffset;
	if (string != '') {
		setMessage(this, string);
		return ;
	}

	offsetCount = calCount(monthOffset, timeDiff);
	string = string + calOffset(offsetCount, 'month');
	timeDiff = timeDiff  - offsetCount * monthOffset;

	offsetCount = calCount(weekOffset, timeDiff);
	string = string + calOffset(offsetCount, 'week');
	timeDiff = timeDiff  - offsetCount * weekOffset;
	if (string != '') {
		setMessage(this, string);
		return ;
	}

	offsetCount = calCount(dayOffset, timeDiff);
	string = string + calOffset(offsetCount, 'day');
	timeDiff = timeDiff  - offsetCount * dayOffset;

	offsetCount = calCount(hourOffset, timeDiff);
	string = string + calOffset(offsetCount, 'hour');
	timeDiff = timeDiff  - offsetCount * hourOffset;

	offsetCount = calCount(minuteOffset, timeDiff);
	string = string + calOffset(offsetCount, 'minute');
	timeDiff = timeDiff  - offsetCount * minuteOffset;
	
	if (string == '') {
		string = 'less than a minute';
	}

	setMessage(this, string);
	return ;

};
