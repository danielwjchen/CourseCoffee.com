<?php

/**
 * Store and translate configurations
 */
class Config{

	/** 
	 * Define site configuration 
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
			if (empty($config['db']['name'])) {
				throw new Exception('missing database name');
			} elseif (empty($config['db']['driver'])) {
				throw new Exception('missing database driver');
			} elseif (empty($config['db']['host'])) {
				throw new Exception('missing database host');
			} elseif (empty($config['db']['port'])) {
				throw new Exception('missing database port');
			} elseif (empty($config['db']['user'])) {
				throw new Exception('missing database user');
			} else if (empty($config['db']['password'])) {
				throw new Exception('missing database password');
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
