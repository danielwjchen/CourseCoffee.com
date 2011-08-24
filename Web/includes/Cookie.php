<?php
/**
 * Manage Cookie usage
 */
class Cookie {

	/**
	 * @defgroup expire
	 * @{
	 * This defines a period of time which the cookie expires
	 */

	const EXPIRE_SESSION = 60;
	const EXPIRE_HOUR    = 60;
	const EXPIRE_DAY     = 1440;
	const EXPIRE_WEEK    = 10080;
	const EXPIRE_MONTH   = 43200;

	/**
	 * @} End expire
	 */

	/**
	 * Set cookie
	 *
	 * Remember to encrypt your data!
	 */
	public static function Set($key, $value, $expire = self::EXPIRE_SESSION, $path = '/', $domain = '') {
		if ($expire = self::EXPIRE_SESSION) {
			setcookie($key, $value);
		} else {
			setcookie($key, $value, time() + $expire, $path, $domain);
		}
	}

	/**
	 * Get cookie
	 *
	 * @param string $key
	 *  a string to identify a cookie variable
	 * @param string $filter
	 *  the ID of the filter to apply
	 *
	 * @return mixed
	 *  value of the cookie variable or null
	 */
	public static function Get($key, $filter = FILTER_UNSAFE_RAW) {
		return filter_input(INPUT_COOKIE, $key, $filter);
	}

	/**
	 * Delete a cookie
	 *
	 * This function empties the value and reset the expire time in the past so
	 * the browser would flush it.
	 *
	 * @param string $key
	 *  a string to identify a cookie variable
	 */
	public static function Del($key) {
		setcookie($key, '', time() - 1000);
	}
}
