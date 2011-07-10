<?php
/**
 * @file
 * The boostrap proccess defines, loads, and includes the necessary files and
 * values to mimic the Core site environment
 */

define('TEST_CORE_PATH', __DIR__);
define('TEST_PATH', str_replace('Core', '', TEST_CORE_PATH));
define('CORE_PATH', str_replace('Test/Core', 'Core/', TEST_CORE_PATH));

require_once TEST_PATH . '/simpletest/autorun.php';
require_once CORE_PATH . 'common/bootstrap.php';

class CoreTestCase extends UnitTestCase{
	protected $config;

	function __construct() {
		parent::__construct();
		$this->config = new Config(array(
			'db' => array(
				'name' => 'qilin_test_core',
				'driver' => 'mysql',
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'bTaEaFaTl2aBl',
				'port' => '3306',
			),
		));

	}

}
