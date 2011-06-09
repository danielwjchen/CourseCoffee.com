<?php

require_once __DIR__ . '/DAOTestCase.php';

class AffiliationLocationLinkageDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

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
		DAOSetup::CleanUp('affiliation_location_linkage');
		$stage = DAOSetup::Prepare('affiliation_location_linkage');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('affiliation_location_linkage');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->affiliation_id == $this->record['affiliation_id'] &&
			$linkage->location_id == $this->record['location_id']);

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
		$linkage->read($this->record);

		$result = ($linkage->affiliation_id == $this->record['affiliation_id'] && 
				$linkage->location_id == $this->record['location_id'] && 
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			affiliation_id - {$linkage->affiliation_id}
			location_id - {$linkage->location_id}
		".print_r($this->record, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);
		$linkage->affiliation_id = 11111;
		$linkage->location_id = 222222;
		$linkage->update();
		$result = (($linkage->affiliation_id != $this->record['affiliation_id'] && 
				$linkage->location_id != $this->record['location_id']) &&
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			affiliation_id - {$linkage->affiliation_id}
			location_id - {$linkage->location_id}
		".print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);

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
