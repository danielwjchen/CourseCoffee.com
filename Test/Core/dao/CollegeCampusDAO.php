<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class CollegeCampusDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'CollegeCampusDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `location`');
		$this->db->perform('TRUNCATE TABLE `location_type`');
		$this->db->perform('TRUNCATE TABLE `affiliation`');
		$this->db->perform('TRUNCATE TABLE `affiliation_location_linkage`');
		$this->defaultParams = array(
			'name' => 'East Lansing',
			'college' => 'Michigan State University',
			'longitude' => mt_rand(),
			'latitude' => mt_rand()
		);

		$this->db->perform(
			'INSERT INTO `affiliation_type` (name) VALUE (:name)',
			array('name' => 'college')
		);

		$this->db->perform(
			'INSERT INTO `affiliation` (name, url, type_id) 
				VALUES (
					:name, 
					:url,
					(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
					)',
			array(
				'name' => 'Michigan State University',
				'url' => 'msu.edu',
				'type' => 'college',
			)
		);
		
		$college = $this->db->fetch(
			"SELECT * FROM affiliation WHERE name = :name",
			array('name' => 'Michigan State University')
		);

		$this->db->perform(
			'INSERT INTO `location_type` (name) VALUE (:name)',
			array('name' => 'college_campus')
		);

		$this->db->perform(
			'INSERT INTO `location` (name, longitude, latitude, type_id)
				VALUES (
					:name, 
					:longitude, 
					:latitude, 
					(SELECT lt.id FROM location_type lt WHERE lt.name = :type))',
			array(
				'name' => $this->defaultParams['name'],
				'longitude' => mt_rand(),
				'latitude' => mt_rand(),
				'type' => 'college_campus',
			)
		);

		$location = $this->db->fetch(
			"SELECT * FROM location WHERE name = :name",
			array('name' => $this->defaultParams['name'])
		);

		$this->db->perform(
			'INSERT INTO `affiliation_location_linkage`
			(affiliation_id, location_id) VALUES (:affiliation_id, :location_id)',
			array(
				'affiliation_id' => $college['id'], 
				'location_id' => $location['id']
			)
		);

		$this->defaultObject = $this->db->fetch("
			SELECT 
				l.*,
				a.id AS college_id,
				a.name AS college
			FROM `location` l
			INNER JOIN affiliation_location_linkage linkage
				ON l.id = linkage.location_id
			INNER JOIN affiliation a
				ON a.id = linkage.affiliation_id
			WHERE	l.name = :name",
			array('name' => $this->defaultParams['name']));

	}

	/**
	 * Tear down test case.
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `location`');
		$this->db->perform('TRUNCATE TABLE `location_type`');
		$this->db->perform('TRUNCATE TABLE `affiliation`');
		$this->db->perform('TRUNCATE TABLE `affiliation_location_linkage`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$college_campus = new CollegeCampusDAO($this->db, $this->defaultParams);
		$result = ($college_campus->id == $this->defaultObject['id']);
		$error = print_r($college_campus, true) . print_r($this->defaultObject, true);

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
			'college' => 'Michigan State University',
		);

		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->create($params);
		$result = ($college_campus->name == $params['name']);
		$error = print_r($college_campus, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->read($this->defaultObject);
		$result = ($college_campus->id == $this->defaultObject['id']);
		$error = print_r($college_campus, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->read($this->defaultObject);
		$college_campus->name = 'asdfsadf';
		$college_campus->update();
		$result = (($college_campus->id == $this->defaultObject['id']) &&
			($college_campus->name != $this->defaultObject['name']));
		$error = print_r($college_campus, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$college_campus = new CollegeCampusDAO($this->db, $this->defaultParams);
		$college_campus->destroy();
		$record = $this->db->perform(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->defaultObject['id'])
		);
		$result = (empty($college_campus->id) && empty($record));
		$error = print_r($college_campus, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
