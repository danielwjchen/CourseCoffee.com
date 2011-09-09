<?php

/**
 * Handle all data encryption/decryption
 */
class Crypto{

	/**
	 * Manage database access
	 */
	private static $db;

	/**
	 *  A unique string to be used as the "salt" for encryption
	 */
	private static $salt;

	/**
	 * Generate a unique string to be used as the "salt" of the encryption
	 *
	 * @return string
	 *  a md5 hash value generated from database configuration
	 */
	public static function generateSalt() {
		global $config;
		return md5(serialize($config->db));
	}

	/**
	 * Create a persistent record in database to store the salt
	 */
	public static function Build() {
		$salt = self::generateSalt();
		self::$db->perform(
			"INSERT INTO crypto (salt, timestamp) VALUES (:salt, UNIX_TIMESTAMP())",
			array('salt' => $salt)
		);
	}

	/**
	 * Initialize Crypto
	 */
	public static function Init($config_db) {
		self::$db = new DB($config_db);
		$record = self::$db->fetch("SELECT * FROM crypto ORDER BY id DESC LIMIT 1");
		self::$salt = $record['salt'];
	}

	/**
	 * Encrypt the string
	 */
	public static function Encrypt($string) {
		return base64_encode(sha1($string. self::$salt, true) . self::$salt); 
	}

	/**
	 * Create a digest of string of a given string
	 *
	 * This is supposed to be fast and insecure.
	 */
	public static function Digest($string) {
		return sha1($string);
	}

}
