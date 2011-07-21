<?php
/**
 * @file
 * Manage sessions
 */
class Session {
	/**
	 * Initialize session
	 */
	public static function Init() {
		session_start();
	}

	/**
	 * Set a session variable
	 *
	 * @param string $key
	 *  a string that identifies a session variable
	 * @param string value
	 *  the value to be stored
	 */
	public static function Set($key, $value) {
		$_SESSION[$key] = $value;
	}

	/**
	 * Get session variable
	 *
	 * @param string $key
	 *  a string that identifies a session variable
	 *
	 * @return mixed 
	 *  the value stored in session or null when it's not found
	 */
	public static function Get($key) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * Delete a session variable
	 *
	 * @param string $key
	 *  a string that identifies a session variable
	 */
	public static function Del($key) {
		unset($_SESSION[$key]);
	}

}
