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
	function __construct() {
		global $config;
		$this->default_db = new DB($config->db['default']);
		$sub_domain = $this->getInstitutionDB();
		if (!empty($sub_domain)) {
			$this->institution_db = new DB($config->db['institution'][$sub_domain]);
		}
	}

	/**
	 * Get institution database
	 *
	 * @return string
	 */
	protected function getInstitutionDB() {
		global $config;
		return str_replace('.', '', str_replace($config->domain, '', $_SERVER['SERVER_NAME']));
	}
}
