<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class LocationDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'LocationDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `location`');
		$this->defaultParams = array(
			'name' => 'East Lansing',
			'longitude' => mt_rand(12, 15),
			'latitude' => mt_rand(12, 15),
			'type' => 'college_campus',
			'type_id' => '',
		);
		$this->db->perform(
			'INSERT INTO `location_type` (name) VALUE (:name)',
			array('name' => $this->defaultParams['type'])
		);

		$location_type = $this->db->fetch(
			'SELECT * FROM `location_type` WHERE `name` = :name',
			array('name' => $this->defaultParams['type'])
		);

		$this->defaultParams['type_id'] = $location_type['id'];

		$this->db->perform(
			'INSERT INTO `location` 
				(name, longitude, latitude, type_id)
			VALUES
				(:name, :longitude, :latitude, :type_id)',
			array(
				'name' => $this->defaultParams['name'],
				'longitude' => $this->defaultParams['longitude'],
				'latitude' => $this->defaultParams['latitude'],
				'type_id' => $this->defaultParams['type_id']
			)
		);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `location` WHERE	name = :name",
			array('name' => $this->defaultParams['name']));


	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `location`');

	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$location = new LocationDAO($this->db, $this->defaultParams);
		$result = ($location->id == $this->defaultObject['id']);
		$error = print_r($location, true) . print_r($this->defaultObject, true);
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
			'type' => 'college_campus'
		);

		$location = new LocationDAO($this->db);
		$location->create($params);
		$result = ($location->name == $params['name']);
		$error = print_r($location, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$location = new LocationDAO($this->db);
		$location->read($this->defaultObject);
		$result = ($location->id == $this->defaultObject['id']);
		$error = print_r($location, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$location = new LocationDAO($this->db);
		$location->read($this->defaultObject);
		$location->name = 'asdfsadf';
		$location->update();
		$result = (($location->id == $this->defaultObject['id']) &&
			($location->name != $this->defaultObject['name']));
		$error = print_r($location, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$location = new LocationDAO($this->db, $this->defaultParams);
		$location->destroy();
		$record = $this->db->perform(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->defaultObject['id'])
		);
		$result = (empty($location->id) && empty($record));
		$error = print_r($location, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
