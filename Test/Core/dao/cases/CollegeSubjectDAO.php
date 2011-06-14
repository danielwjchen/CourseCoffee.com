<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeSubjectDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'CollegeSubjectDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('college_subject');
		$stage = DAOSetup::Prepare('college_subject');
		$this->record = $stage['record'];
		$this->params = $stage['params'];

	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('college_subject');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college = new CollegeSubjectDAO($this->db, $this->params);
		$result = ($college->id == $this->record['id']);
		$error = print_r($college, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'subject' => 'Computer Science and Engineering',
			'description' => 'Most awesome subject to study in college',
			'college' => $this->params['college'],
			'college_id' => $this->params['college_id'],
			'abbr' => 'CSE',
		);

		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->create($params);
		$result = ($college_subject->abbr == $params['abbr']);
		$error = print_r($college_subject, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->read($this->record);
		$result = ($college_subject->id == $this->record['id']);
		$error = print_r($college_subject, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->read($this->record);
		$college_subject->abbr = 'asdfsadf';
		$college_subject->update();
		$result = (($college_subject->id == $this->record['id']) &&
			($college_subject->abbr != $this->record['abbr']));
		$error = print_r($college_subject, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$college_subject = new CollegeSubjectDAO($this->db, $this->params);
		$college_subject->destroy();
		$record = $this->db->perform(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_subject->id) && empty($record));
		$error = print_r($college_subject, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
