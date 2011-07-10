<?php

require_once __DIR__ . '/DAOTestCase.php';

class ItemTypeDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'ItemTypeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('item_type');
		$stage = DAOSetup::Prepare('item_type');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('item_type');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$type = new ItemTypeDAO($this->db, $this->params);

		$result = ($type->id == $this->record['id'] &&
			$type->name == $this->record['name']);

		$error = "
			id - {$type->id}
			type - {$type->name}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array('name' => mt_rand(4,5));
		$type = new ItemTypeDAO($this->db);
		$type->create($params);
		$result = ($type->name == $params['name']);

		$error = print_r($params, true) . "\n" . print_r($type, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$type = new ItemTypeDAO($this->db);
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
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$type = new ItemTypeDAO($this->db, $this->params);
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
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$type = new ItemTypeDAO($this->db, $this->params);
		$result = (empty($type->name) && empty($type->id));

		$error = "
			name - {$type->name}
			id - {$type->id}
		";

		$this->assertTrue($result, $error);

	}

}
