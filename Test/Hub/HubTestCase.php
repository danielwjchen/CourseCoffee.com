<?php

/**
 * Base class of all test cases in Hub
 */
abstract class HubTestCase extends UnitTestCase{

	protected $config;
	protected $db;

	/**
	 * Extend UniTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();

		$this->config = new Config(array(
			'db' => array(
				'name' => 'plnnr_test',
				'driver' => 'mysql',
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'bTaEaFaTl2aBl',
				'port' => '3306',
			),
		));

    $this->db = new DB($this->config->db);

	}

}
