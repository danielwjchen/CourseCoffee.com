<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class CollegeDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'CollegeDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('affiliation');
		$stage = DAOSetup::Prepare('affiliation');
		$this->record = $stage['record'];
		$this->params = $stage['params'];

	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('affiliation');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$college = new CollegeDAO($this->db, $this->params);
		$result = ($college->id == $this->record['id']);
		$error = print_r($college, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Michigan State University',
			'url' => mt_rand(),
		);

		$college = new CollegeDAO($this->db);
		$college->create($params);
		$result = ($college->name == $params['name']);
		$error = print_r($college, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$college = new CollegeDAO($this->db);
		$college->read($this->record);
		$result = ($college->id == $this->record['id']);
		$error = print_r($college, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$college = new CollegeDAO($this->db);
		$college->read($this->record);
		$college->name = 'asdfsadf';
		$college->update();
		$result = (($college->id == $this->record['id']) &&
			($college->name != $this->record['name']));
		$error = print_r($college, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$college = new CollegeDAO($this->db, $this->params);
		$college->destroy();
		$record = $this->db->perform(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college->id) && empty($record));
		$error = print_r($college, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
