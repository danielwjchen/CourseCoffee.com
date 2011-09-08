<?php
/**
 * @file
 * Define the basic structures of a model class
 */
interface ModelInterface {
}

/**
 * This class defines methods that are commonly shared by all models
 */
abstract class Model {

	protected $default_db;
	protected $institution_db;

	/**
	 * Construct the model object
	 */
	function __construct($domain) {
		global $config;
		$this->default_db = new DB($config->db['default']);
		if ($domain != 'www') {
			$this->institution_db = new DB($config->db['institution'][$domain]);
		}
	}

}
