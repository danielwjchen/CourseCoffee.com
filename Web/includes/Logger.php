<?php
/**
 * @file
 * Manage logging
 */
class Logger {

	/**
	 * @defgroup severity level
	 * @{
	 */
	
	/**
	 * Define events that are on notice, e.g. successful user registration, 
	 * failed login attemp.
	 */
	const SEVERITY_NOTICE = 'NOTICE';

	/**
	 * Define events that are important but not a threat, e.g. 404 page not found.
	 */
	const SEVERITY_LOW = 'LOW';

	/**
	 * Define events that require immediate attention, e.g. excessive login 
	 * attempts, spamming.
	 */
	const SEVERITY_HIGH = 'HIGH';

	/**
	 * }@ End group severity level
	 */

	/**
	 * Initialize the logger
	 */
	public static function Init() {
	}

	/**
	 * Write event to log
	 *
	 * @param string $event
	 *  a string that describes the event
	 * @param string $severity
	 *  a string that describes the severity of the event, default to NOTE
	 */
	public static function Write($event, $severity = self::SEVERITY_NOTICE) {
		error_log($severity . ' - ' . $event);
	}
}
