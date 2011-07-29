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
	protected $db;

	/**
	 * Construct the model object
	 */
	function __construct() {
		global $config;
		$this->db = new DB($config->db);
	}
}
