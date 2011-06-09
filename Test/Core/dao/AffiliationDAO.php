<?php

require_once __DIR__ . '/DAOTestCase.php';

class AffiliationDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

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
		$affiliation = new AffiliationDAO($this->db, $this->params);
		$result = ($affiliation->id == $this->record['id']);
		$error = print_r($affiliation, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Michigan State University',
			'url' => mt_rand(),
			'type' => $this->params['type']
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
		$affiliation->read($this->record);
		$result = ($affiliation->id == $this->record['id']);
		$error = print_r($affiliation, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$affiliation = new AffiliationDAO($this->db);
		$affiliation->read($this->record);
		$affiliation->name = 'asdfsadf';
		$affiliation->update();
		$result = (($affiliation->id == $this->record['id']) &&
			($affiliation->name != $this->record['name']));
		$error = print_r($affiliation, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$affiliation = new AffiliationDAO($this->db, $this->params);
		$affiliation->destroy();
		$record = $this->db->perform(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($affiliation->id) && empty($record));
		$error = print_r($affiliation, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
