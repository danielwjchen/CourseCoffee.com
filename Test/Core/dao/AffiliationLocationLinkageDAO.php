<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class AffiliationLocationLinkageDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'AffiliationLocationLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `affiliation_location_linkage`');
		$this->defaultParams = array(
			'affiliation_id' => mt_rand(),
			'location_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `affiliation_location_linkage` (affiliation_id, location_id) 
				VALUES (:affiliation_id, :location_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `affiliation_location_linkage` 
			WHERE affiliation_id = :affiliation_id AND location_id = :location_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `affiliation_location_linkage`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->affiliation_id == $this->defaultObject['affiliation_id'] &&
			$linkage->location_id == $this->defaultObject['location_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->affiliation_id}
			child - {$linkage->location_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$affiliation_ids = array('affiliation_id' => mt_rand(), 'location_id' => mt_rand());
		$linkage = new AffiliationLocationLinkageDAO($this->db);
		$linkage->create($affiliation_ids);
		$result = ($linkage->affiliation_id == $affiliation_ids['affiliation_id'] &&
			$linkage->location_id == $affiliation_ids['location_id']);

		$error = print_r($affiliation_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$linkage = new AffiliationLocationLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->affiliation_id == $this->defaultObject['affiliation_id'] && 
				$linkage->location_id == $this->defaultObject['location_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			affiliation_id - {$linkage->affiliation_id}
			location_id - {$linkage->location_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->defaultParams);
		$linkage->affiliation_id = 'foo';
		$linkage->location_id = 'bar';
		$linkage->update();
		$result = (($linkage->affiliation_id != $this->defaultObject['affiliation_id'] && 
				$linkage->location_id != $this->defaultObject['location_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			affiliation_id - {$linkage->affiliation_id}
			location_id - {$linkage->location_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->affiliation_id) && 
				empty($linkage->location_id) &&
				empty($linkage->id));

		$error = "
			affiliation_id - {$linkage->affiliation_id}
			location_id - {$linkage->location_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
