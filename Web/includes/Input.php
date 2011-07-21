<?php
/**
 * @file
 * Handle HTTP POST and HTTP GET inputs
 */

class Input {

	/**
	 * Return POST variables
	 *
	 * @param string $key
	 *  a string to identify a POST variable
	 * @param string $filter
	 *  the ID of the filter to apply
	 *
	 * @return mixed
	 *  value of the POST variable or null
	 */
	public static function Post($key, $filter = FILTER_UNSAFE_RAW) {
		return filter_input(INPUT_POST, $key, $filter);
	}

	/**
	 * Return GET variables
	 *
	 * @param string $key
	 *  a string to identify a cookie variable
	 * @param string $filter
	 *  the ID of the filter to apply
	 *
	 * @return mixed
	 *  value of the GET variable or null
	 */
	public static function Get($key, $filter = FILTER_UNSAFE_RAW) {
		return filter_input(INPUT_GET, $key, $filter);
	}

}
