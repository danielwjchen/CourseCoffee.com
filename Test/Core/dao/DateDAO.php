<?php

require_once __DIR__ . '/DAOTestCase.php';

class DateDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'DateDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('date');
		$stage = DAOSetup::Prepare('date');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('date');

	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$date = new DateDAO($this->db, $this->params);
		$result = ($date->id == $this->record['id']);
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'timestamp' => mt_rand(0, time()),
			'type' => $this->params['type']
		);

		$date = new DateDAO($this->db);
		$date->create($params);
		$result = ($date->timestamp == $params['timestamp']);
		$error = print_r($date, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$date = new DateDAO($this->db);
		$date->read($this->record);
		$result = ($date->id == $this->record['id']);
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$date = new DateDAO($this->db);
		$date->read($this->record);
		$date->timestamp = mt_rand(0, time());
		$date->update();
		$result = (($date->id == $this->record['id']) &&
			($date->timestamp != $this->record['timestamp']));
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$date = new DateDAO($this->db, $this->params);
		$date->destroy();
		$record = $this->db->perform(
			'SELECT * FROM date WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($date->id) && empty($record));
		$error = print_r($date, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
