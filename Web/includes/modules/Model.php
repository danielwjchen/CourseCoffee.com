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

	const DEFAULT_DB     = 'default_db';
	const INSTITUTION_DB = 'institution_db';

	protected $default_db;
	protected $institution_db;

	/**
	 * An array of dao resources
	 */
	protected $dao;

	/**
	 * Construct the model object
	 */
	function __construct($domain) {
		global $config;
		$this->default_db = new DB($config->db['default']);
		if ($domain != 'www' && $domain != '') {
			$this->institution_db = new DB($config->db['institution'][$domain]);
		}

		if (method_exists($this, 'defineDAO')) {
			$this->instantiateDAO($this->defineDAO());
		}

	}

	/**
	 * Instantiate DAOs
	 */
	private function instantiateDAO($dao_array) {
		foreach ($dao_array as $name => $definition) {
			$this->dao[$name] = new $definition['dao']($this->$definition['db']);
		}
	}

}
