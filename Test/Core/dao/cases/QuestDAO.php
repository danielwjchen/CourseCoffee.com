<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'QuestDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('quest');
		$stage = DAOSetup::Prepare('quest');
		$this->record = $stage['record'];
		$this->params = $stage['params'];

	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('quest');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$quest = new QuestDAO($this->db, $this->params);
		$result = ($quest->id == $this->record['id']);
		$error = print_r($quest, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'type' => $this->params['type'],
			'user_id' => $this->params['user_id'],
			'objective' => 'foo',
			'description' => 'bar',
		);

		$quest = new QuestDAO($this->db);
		$quest->create($params);
		$result = ($quest->objective == $params['objective']);
		$error = print_r($quest, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$quest = new QuestDAO($this->db);
		$quest->read($this->record);
		$result = ($quest->id == $this->record['id']);
		$error = print_r($quest, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$quest = new QuestDAO($this->db);
		$quest->read($this->record);
		$quest->objective = 'asdfsadfasdfasdfasd';
		$quest->update();
		$result = (($quest->id == $this->record['id']) &&
			($quest->objective != $this->record['objective']));
		$error = print_r($quest, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$quest = new QuestDAO($this->db, $this->params);
		$quest->destroy();
		$record = $this->db->perform(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($quest->id) && empty($record));
		$error = print_r($quest, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
