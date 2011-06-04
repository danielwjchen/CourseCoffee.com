<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class CollegeDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

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
		$this->db->perform('TRUNCATE TABLE `affiliation`');
		$this->db->perform('TRUNCATE TABLE `affiliation_type`');
		$this->db->perform(
			'INSERT INTO `affiliation_type` (name) VALUE (:name)',
			array('name' => 'college')
		);

		$this->defaultParams = array(
			'name' => 'Department of Science and Enginerring',
			'url' => mt_rand(12, 15),
		);

		$this->db->perform(
			'INSERT INTO `affiliation` (name, url, type_id) 
				VALUES (
					:name, 
					:url,
					(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
					)',
			array(
				'name' => $this->defaultParams['name'],
				'url' => $this->defaultParams['url'],
				'type' => 'college',
			)
		);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `affiliation` WHERE	name = :name",
			array('name' => $this->defaultParams['name']));


	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `affiliation`');
		$this->db->perform('TRUNCATE TABLE `affiliation_type`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$college = new CollegeDAO($this->db, $this->defaultParams);
		$result = ($college->id == $this->defaultObject['id']);
		$error = print_r($college, true) . print_r($this->defaultObject, true);

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
		$college->read($this->defaultObject);
		$result = ($college->id == $this->defaultObject['id']);
		$error = print_r($college, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$college = new CollegeDAO($this->db);
		$college->read($this->defaultObject);
		$college->name = 'asdfsadf';
		$college->update();
		$result = (($college->id == $this->defaultObject['id']) &&
			($college->name != $this->defaultObject['name']));
		$error = print_r($college, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$college = new CollegeDAO($this->db, $this->defaultParams);
		$college->destroy();
		$record = $this->db->perform(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->defaultObject['id'])
		);
		$result = (empty($college->id) && empty($record));
		$error = print_r($college, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
