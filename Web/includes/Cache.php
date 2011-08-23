<?php
/**
 * @file
 * Cache data using different schems to speed up the sys
 */

interface CacheInterface {
	/**
	 * Get the cached value from given key
	 *
	 * @param string $key
	 */
	public function Get($key) ;

	/**
	 * Set cache for the given key and value
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function Set($key, $value) ;

	/**
	 * Delete cached value
	 *
	 * @param string $key
	 */
	public function Del($key);
}

/**
 * Cache data in database
 */
class DBCache implements CacheInterface {

	private $db;

	function __construct() {
		global $config;
		$this->db = new DB($config->db);
	}

	function __destruct() {
    $this->db = null;
	}

	/**
	 * Implement CacheInterface::Get();
	 */
	public function Get($key) {
		return $this->db->fetchList(
			'SELECT `value` FROM `cache` WHERE `key` = :key',
			array('key' => $key)
		);
	}

	/**
	 * Implement CacheInterface::Set();
	 */
	public function Set($key, $value) {
		$this->db->perform(
			'INSERT INTO `cache` (`key`, `value`) VALUES (:key, :value)',
			array('key' => $key, 'value' => $value)
		);
	}

	/**
	 * Implement CacheInterface::Del();
	 */
	public function Del($key) {
		$this->db->perform(
			'DELETE FROM `cache` WHERE `key` = :key', 
			array('key',  $key)
		);
	}
}
