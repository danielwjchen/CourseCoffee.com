<?php

require_once __DIR__ . '/../bootstrap.php';

class LocationDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'LocationDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('location');
		$stage = DAOSetup::Prepare('location');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('location');

	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$location = new LocationDAO($this->db, $this->params);
		$result = ($location->id == $this->record['id']);
		$error = print_r($location, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Lake Oswego',
			'longitude' => mt_rand(),
			'latitude' => mt_rand(),
			'type' => 'college_campus'
		);

		$location = new LocationDAO($this->db);
		$location->create($params);
		$result = ($location->name == $params['name']);
		$error = print_r($location, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$location = new LocationDAO($this->db);
		$location->read($this->record);
		$result = ($location->id == $this->record['id']);
		$error = print_r($location, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$location = new LocationDAO($this->db);
		$location->read($this->record);
		$location->name = 'asdfsadf';
		$location->update();
		$result = (($location->id == $this->record['id']) &&
			($location->name != $this->record['name']));
		$error = print_r($location, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$location = new LocationDAO($this->db, $this->params);
		$location->destroy();
		$record = $this->db->perform(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($location->id) && empty($record));
		$error = print_r($location, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
