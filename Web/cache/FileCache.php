<?php
/**
 * @file
 * Cache data in files
 *
 * This is only faster than DBCache() for a fraction of milliseconds, but 
 * performes badly when system is under heavy IO load.
 *
 * File cache still uses the database to manage set() and expire().
 */
class FileCache implements CacheInterface {

	private $db;

	function __destruct() {
    $this->db = null;
	}

	/**
	 * Setup database connection
	 */
	private function connectDB() {
		global $config;
		$this->db = new DB($config->db);
	}

	/**
	 * Implement CacheInterface::get()
	 */
	public function get($key) {
		return file_get_contents(FILE_CACHE_PATH . '/' . $key);
	}

	/**
	 * Implement CacheInterface::set()
	 */
	public function set($key, $value, $expire = 0) {
		$this->connectDB();
		$this->db->perform(
			'REPLACE INTO `file_cache` (`key`, `created`, `expire`) 
			VALUES (:key, UNIX_TIMESTAMP(), :expire)',
			array('key' => $key, 'expire' => $expire)
		);

		file_put_contents(FILE_CACHE_PATH . '/' . $key, $value);
	}

	/**
	 * Implement CacheInterface::del()
	 */
	public function del($key) {
		$this->connectDB();
		$this->db->perform(
			'DELETE FROM `file_cache` WHERE `key` = :key', 
			array('key',  $key)
		);
		unlink(FILE_CACHE_PATH . '/' . $key);
	}

	/**
	 * Implement CacheInterface::expire()
	 */
	public function expire($timestamp = NULL) {
		$this->connectDB();
		$records = $this->db->fetchList(
			'SELECT * FROM `file_cache` WHERE `expire` <=:timestamp',
			array('timestamp' => $timestamp)
		);
		foreach ($records AS $record) {
			unlink(FILE_CACHE_PATH . '/' . $record['key']);
		}
		$this->db->perform(
			'DELETE FROM `file_cache` WHERE `expire` <= :timestamp',
			array('timestamp' => $timestamp)
		);
	}

}
