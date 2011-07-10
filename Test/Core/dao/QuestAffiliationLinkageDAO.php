<?php

require_once __DIR__ . '/DAOTestCase.php';

class QuestAffiliationLinkageDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $record;
	protected $params;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestAffiliationLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		DAOSetup::CleanUp('quest_affiliation_linkage');
		$stage = DAOSetup::Prepare('quest_affiliation_linkage');
		$this->record = $stage['record'];
		$this->params = $stage['params'];
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		DAOSetup::CleanUp('quest_affiliation_linkage');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->affiliation_id == $this->record['affiliation_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->quest_id}
			child - {$linkage->affiliation_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('quest_id' => mt_rand(), 'affiliation_id' => mt_rand());
		$linkage = new QuestAffiliationLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->quest_id == $quest_ids['quest_id'] &&
			$linkage->affiliation_id == $quest_ids['affiliation_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$linkage = new QuestAffiliationLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = ($linkage->quest_id == $this->record['quest_id'] && 
				$linkage->affiliation_id == $this->record['affiliation_id'] && 
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			affiliation_id - {$linkage->affiliation_id}
		".print_r($this->record, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 'foo';
		$linkage->affiliation_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->record['quest_id'] && 
				$linkage->affiliation_id != $this->record['affiliation_id']) &&
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			affiliation_id - {$linkage->affiliation_id}
		".print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);

		$result = (empty($linkage->quest_id) && 
				empty($linkage->affiliation_id) &&
				empty($linkage->id));

		$error = "
			quest_id - {$linkage->quest_id}
			affiliation_id - {$linkage->affiliation_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
