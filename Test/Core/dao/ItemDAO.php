<?php

require_once __DIR__ . '/DAOTestCase.php';

class ItemDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'ItemDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('item');
		$stage = DAOSetup::Prepare('item');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('item');

	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$item = new ItemDAO($this->db, $this->params);
		$result = ($item->id == $this->record['id']);
		$error = print_r($item, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Lake Oswego',
			'longitude' => mt_rand(),
			'latitude' => mt_rand(),
			'type' => 'college_campus'
		);

		$item = new ItemDAO($this->db);
		$item->create($params);
		$result = ($item->name == $params['name']);
		$error = print_r($item, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$item = new ItemDAO($this->db);
		$item->read($this->record);
		$result = ($item->id == $this->record['id']);
		$error = print_r($item, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$item = new ItemDAO($this->db);
		$item->read($this->record);
		$item->name = 'asdfsadf';
		$item->update();
		$result = (($item->id == $this->record['id']) &&
			($item->name != $this->record['name']));
		$error = print_r($item, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$item = new ItemDAO($this->db, $this->params);
		$item->destroy();
		$record = $this->db->perform(
			'SELECT * FROM item WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($item->id) && empty($record));
		$error = print_r($item, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
