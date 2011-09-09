<?php
/**
 * @file
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
	 * Implement CacheInterface::get()
	 */
	public function get($key) {
		$result = $this->db->fetch(
			'SELECT `value` FROM `cache` WHERE `key` = :key',
			array('key' => $key)
		);

		return $result['value'];
	}

	/**
	 * Implement CacheInterface::set()
	 */
	public function set($key, $value, $expire = 0) {
		$this->db->perform(
			'REPLACE INTO `cache` (`key`, `value`, `created`, `expire`) 
			VALUES (:key, :value, UNIX_TIMESTAMP(), :expire)',
			array('key' => $key, 'value' => $value, 'expire' => $expire)
		);
	}

	/**
	 * Implement CacheInterface::del()
	 */
	public function del($key) {
		$this->db->perform(
			'DELETE FROM `cache` WHERE `key` = :key', 
			array('key',  $key)
		);
	}

	/**
	 * Implement CacheInterface::expire()
	 */
	public function expire($timestamp = NULL) {
		$timestamp = empty($timestamp) ? time() : $timestamp;
		$this->db->perform(
			'DELETE FROM `cache` WHERE `expire` <= :timestamp',
			array('timestamp' => $timestamp)
		);
	}
}
