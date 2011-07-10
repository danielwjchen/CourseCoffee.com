<?php

/**
 * Set up the environment for testing Hub
 */
abstract class HubSetup{

  static protected $db;

  /**
   * Initialize Setup
   *
   * @param array $db_config
   *  an associative array that defines the database connection
   */
  static public function Init($db_config) {
    self::$db = new DB($db_config);
		Factory::Init($db_config);
  }

	/**
	 * Generate a random string of words
	 *
	 * @param string $max
	 *  the maximun number of characters of the string, default to 24
	 */
	static protected function generateString($max = 24) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890 ';
		$charLength = strlen($chars) - 1;
		$string = '';

		for ($i = 0; $i < $max; $i++) {
			$string .= $chars[mt_rand(0, $charLength)];
		}

		return $string;

	}

}
