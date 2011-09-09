<?php
/**
 * @file
 * Cache data using different schems to speed up the sys
 *
 * This is only a inteface template. Actual implementations are kept under /cache
 */

interface CacheInterface {
	/**
	 * Get the cached value from given key
	 *
	 * @param string $key
	 */
	public function get($key);

	/**
	 * Set cache for the given key and value
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value, $expire = 0);

	/**
	 * Delete cached value
	 *
	 * @param string $key
	 */
	public function del($key);


	/**
	 * Expire values in cache
	 */
	public function expire();
}

