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

	/**
	 * Manage database access
	 */
	protected $sys_db;
	protected $core_db;

	/**
	 * Construct the model object
	 */
	function __construct() {
		global $config;
		$this->sys_db = new DB($config->db['sys']);
		$this->core_db = new DB($config->db['core']);
	}
}
