<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestTypeDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestTypeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('quest_type');
		$stage = DAOSetup::Prepare('quest_type');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('quest_type');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$type = new QuestTypeDAO($this->db, $this->params);

		$result = ($type->id == $this->record['id'] &&
			$type->name == $this->record['name']);

		$error = "
			id - {$type->id}
			type - {$type->name}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array('name' => mt_rand(0,5));
		$type = new QuestTypeDAO($this->db);
		$type->create($params);
		$result = ($type->name == $params['name']);

		$error = print_r($params, true) . "\n" . print_r($type, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$type = new QuestTypeDAO($this->db);
		$type->read($this->record);

		$result = ($type->name == $this->record['name'] && 
				$type->id == $this->record['id']);

		$error = "
			id - {$type->id}
			name - {$type->name}
		".print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$type = new QuestTypeDAO($this->db, $this->params);
		$type->name = 'foo bar';
		$type->update();
		$result = ($type->name != $this->record['name'] &&
				$type->id == $this->record['id']);

		$error = "
			id - {$type->id}
			name - {$type->name}
		".print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$type = new QuestTypeDAO($this->db, $this->params);
		$result = (empty($type->name) && empty($type->id));

		$error = "
			name - {$type->name}
			id - {$type->id}
		";

		$this->assertTrue($result, $error);

	}

}
