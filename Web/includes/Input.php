<?php
/**
 * @file
 * Handle HTTP POST and HTTP GET inputs
 */

class Input {

	/**
	 * Return POST variables
	 */
	public static function Post($key, $filter = FILTER_UNSAFE_RAW) {
		return filter_input(INPUT_POST, $key, $filter);
	}

	/**
	 * Return GET variables
	 */
	public static function Get($key, $filter = FILTER_UNSAFE_RAW) {
		return filter_input(INPUT_GET, $key, $filter);
	}

}
