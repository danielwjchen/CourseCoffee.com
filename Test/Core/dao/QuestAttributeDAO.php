<?php

require_once __DIR__ . '/DAOTestCase.php';

class QuestAttributeDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'QuestAttributeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('quest_attribute');
		$stage = DAOSetup::Prepare('quest_attribute');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('quest_attribute');

	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$quest_attribute = new QuestAttributeDAO($this->db, $this->params);
		$result = ($quest_attribute->id == $this->record['id']);
		$error = print_r($quest_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'value' => 'foo bar',
			'quest_id' => $this->params['quest_id'],
			'type' => $this->params['type'],
		);

		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->create($params);
		$result = ($quest_attribute->value == $params['value']);
		$error = print_r($quest_attribute, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->read($this->params);
		$result = ($quest_attribute->id == $this->record['id']);
		$error = print_r($quest_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->read($this->record);
		$quest_attribute->value = 'asdfsadf';
		$quest_attribute->update();
		$result = (($quest_attribute->id == $this->record['id']) &&
			($quest_attribute->value != $this->record['value']));
		$error = print_r($quest_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$quest_attribute = new QuestAttributeDAO($this->db, $this->params);
		$quest_attribute->destroy();
		$record = $this->db->perform(
			'SELECT * FROM quest_attribute WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($quest_attribute->id) && empty($record));
		$error = print_r($quest_attribute, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
