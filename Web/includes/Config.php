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
		$this->data = $config;
	}

	/**
	 * Get a configuration value
	 */
	function __get($name) {
		// builds are made daily
		if ($name == 'build') {
			return date('d');
		}
		return $this->data[$name];
	}
}
