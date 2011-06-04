<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class AffiliationDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH . 'AffiliationDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `affiliation`');
		$this->db->perform('TRUNCATE TABLE `affiliation_type`');
		$this->defaultParams = array(
			'name' => 'Department of Science and Enginerring',
			'url' => mt_rand(12, 15),
			'type' => 'college',
			'type_id' => '',
		);
		$this->db->perform(
			'INSERT INTO `affiliation_type` (name) VALUE (:name)',
			array('name' => $this->defaultParams['type'])
		);

		$affiliation_type = $this->db->fetch(
			'SELECT * FROM `affiliation_type` WHERE `name` = :name',
			array('name' => $this->defaultParams['type'])
		);

		$this->defaultParams['type_id'] = $affiliation_type['id'];

		$this->db->perform(
			'INSERT INTO `affiliation` (name, url, type_id) 
				VALUES (:name, :url, :type_id)',
			array(
				'name' => $this->defaultParams['name'],
				'url' => $this->defaultParams['url'],
				'type_id' => $this->defaultParams['type_id']
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
		$affiliation = new AffiliationDAO($this->db, $this->defaultParams);
		$result = ($affiliation->id == $this->defaultObject['id']);
		$error = print_r($affiliation, true) . print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Michigan State University',
			'url' => mt_rand(),
			'type' => 'college'
		);

		$affiliation = new AffiliationDAO($this->db);
		$affiliation->create($params);
		$result = ($affiliation->name == $params['name']);
		$error = print_r($affiliation, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$affiliation = new AffiliationDAO($this->db);
		$affiliation->read($this->defaultObject);
		$result = ($affiliation->id == $this->defaultObject['id']);
		$error = print_r($affiliation, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$affiliation = new AffiliationDAO($this->db);
		$affiliation->read($this->defaultObject);
		$affiliation->name = 'asdfsadf';
		$affiliation->update();
		$result = (($affiliation->id == $this->defaultObject['id']) &&
			($affiliation->name != $this->defaultObject['name']));
		$error = print_r($affiliation, true) . print_r($this->defaultObject, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$affiliation = new AffiliationDAO($this->db, $this->defaultParams);
		$affiliation->destroy();
		$record = $this->db->perform(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->defaultObject['id'])
		);
		$result = (empty($affiliation->id) && empty($record));
		$error = print_r($affiliation, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
