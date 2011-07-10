<?php

abstract class DAOTestCase extends CoreTestCase{

	protected $db;
	protected $record;
	protected $params;
	protected $case;

	function __construct() {
		parent::__construct();
		$this->db = new DB($this->config->db);
		Factory::Init($this->config->db);
    DAOSetup::Init($this->config->db);
		if (isset($this->case)) {
			require_once DAO_PATH . $this->case . '.php';

		}
	}

	/**
	 * Set up test case.
	 */
  public function setUp() {
		DAOSetup::CleanUp($this->case);
		$stage = DAOSetup::Prepare($this->case, $params);
		$this->record = $stage['record'];
		$this->params = $stage['params'];
  }

	/**
	 * Tear down test case.
	 */
  public function tearDown() {
		DAOSetup::CleanUp($this->case);

  }

}
