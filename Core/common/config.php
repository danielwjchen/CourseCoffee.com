<?php

/**
 * Store and translate configurations
 */
class Config{

	/** 
	 * Define site configuration 
	 *
	 */
	private $data;

	/**
	 * Construct a singleton object
	 *
	 * @param array $config
	 *  an associative array of configuration values that defines a site
	 */
	function __construct(array $config) {
		if (empty($config['db'])) {
			throw new Exception('missing database(s)');
		} else {
			foreach ($config['db'] as $db) {
				if (empty($db['name'])) {
					throw new Exception('missing database name');
				} elseif (empty($db['driver'])) {
					throw new Exception('missing database driver');
				} elseif (empty($db['host'])) {
					throw new Exception('missing database host');
				} elseif (empty($db['port'])) {
					throw new Exception('missing database port');
				} elseif (empty($db['user'])) {
					throw new Exception('missing database user');
				} else if (empty($db['password'])) {
					throw new Exception('missing database password');
				}
			}
		}

		$this->data = $config;
	}

	/**
	 * Get a configuration value
	 */
	function __get($name) {
		return $this->data[$name];
	}
}
