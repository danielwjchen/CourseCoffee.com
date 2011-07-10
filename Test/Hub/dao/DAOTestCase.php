<?php

/**
 * Base class of all test cases in dao.
 */
abstract class DAOTestCase extends HubTestCase{

	/**
	 * The record in database saved in an associateve array
	 */
	protected $record;

	/**
	 * The parameters used to create the record in database
	 */
	protected $params;

  /**
	 * The DAOSetup class responsible for setting up the environment.
	 */
	protected $dao_setup;

	/**
	 * Type of parameters to be generate from DAOSetup
	 */
	protected $param_type;

	/**
	 * Extend HubTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->dao_setup = $this->getDAOSetupName(get_class($this));
		$this->param_type = array('random');

		$dao_class = str_replace('TestCase', '', get_class($this));

		require_once TEST_CORE_DAO_PATH . '/setups/' . $this->dao_setup . '.php';

		if (preg_match('/college/i', $dao_class)) {
			require_once DAO_PATH . '/college/' . $dao_class . '.php';

		} else {
			require_once DAO_PATH . '/core/' . $dao_class . '.php';

		}

		call_user_func($this->dao_setup . '::Init', $this->config->db);
	}

	/**
	 * Get name of the DAOSetup class fram a DAOTestCase class
	 *
	 * @param string $dao_test_case
	 *  name of the DAOTestCase class
	 *
	 * @return string
	 *  name of the DAOSetup class
	 */
	protected function getDAOSetupName($class_name) {
		return str_replace('DAOTestCase', 'DAOSetup', $class_name);
	}


	/**
	 * Set up the test case.
	 */
  public function setUp() {
		$stage = call_user_func($this->dao_setup . '::Prepare', $this->param_type);
		$this->record = $stage['record'];
		$this->params = $stage['params'];
  }

	/**
	 * Tear down the test case.
	 */
  public function tearDown() {
		call_user_func($this->dao_setup . '::CleanUp');

  }

}
