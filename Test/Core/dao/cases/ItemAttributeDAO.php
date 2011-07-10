<?php

require_once __DIR__ . '/../bootstrap.php';

class ItemAttributeDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'ItemAttributeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('item_attribute');
		$stage = DAOSetup::Prepare('item_attribute');
		print_r($stage);
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('item_attribute');

	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$item_attribute = new ItemAttributeDAO($this->db, $this->params);
		$result = ($item_attribute->id == $this->record['id']);
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'value' => 'foo bar',
			'item_id' => $this->params['item_id'],
			'type' => $this->params['type'],
		);

		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->create($params);
		$result = ($item_attribute->value == $params['value']);
		$error = print_r($item_attribute, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->read($this->params);
		$result = ($item_attribute->id == $this->record['id']);
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->read($this->record);
		$item_attribute->value = 'asdfsadf';
		$item_attribute->update();
		$result = (($item_attribute->id == $this->record['id']) &&
			($item_attribute->value != $this->record['value']));
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$item_attribute = new ItemAttributeDAO($this->db, $this->params);
		$item_attribute->destroy();
		$record = $this->db->perform(
			'SELECT * FROM item_attribute WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($item_attribute->id) && empty($record));
		$error = print_r($item_attribute, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
