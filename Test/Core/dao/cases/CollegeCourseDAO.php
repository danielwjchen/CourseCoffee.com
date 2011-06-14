<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeCourseDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'CollegeCourseDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('college_course');
		$stage = DAOSetup::Prepare('college_course');
		$this->record = $stage['record'];
		$this->params = $stage['params'];

	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('college_course');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_course = new CollegeCourseDAO($this->db, $this->params);
		$result = ($college_course->id == $this->record['id']);
		$error = print_r($college_course, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'title' => 'Introduction to C++',
			'description' => 'Most awesome class to study in college',
			'subject' => $this->params['subject'],
			'subject_id' => $this->params['subject_id'],
			'college' => $this->params['college'],
			'college_id' => $this->params['college_id'],
			'num' => '131',
		);

		$college_course = new CollegeCourseDAO($this->db);
		$college_course->create($params);
		$result = ($college_course->num == $params['num']);
		$error = print_r($college_course, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$college_course = new CollegeCourseDAO($this->db);
		$college_course->read($this->record);
		$result = ($college_course->id == $this->record['id']);
		$error = print_r($college_course, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$college_course = new CollegeCourseDAO($this->db);
		$college_course->read($this->record);
		$college_course->num = 'asdfsadf';
		$college_course->update();
		$result = (($college_course->id == $this->record['id']) &&
			($college_course->num != $this->record['num']));
		$error = print_r($college_course, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$college_course = new CollegeCourseDAO($this->db, $this->params);
		$college_course->destroy();
		$record = $this->db->perform(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_course->id) && empty($record));
		$error = print_r($college_course, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
