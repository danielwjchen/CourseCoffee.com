<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestPersonLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestPersonLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `quest_person_linkage`');
		$this->defaultParams = array(
			'quest_id' => mt_rand(),
			'person_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `quest_person_linkage` (quest_id, person_id) 
				VALUES (:quest_id, :person_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `quest_person_linkage` 
			WHERE quest_id = :quest_id AND person_id = :person_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `quest_person_linkage`');
	}

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestPersonLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->quest_id == $this->defaultObject['quest_id'] &&
			$linkage->person_id == $this->defaultObject['person_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->quest_id}
			child - {$linkage->person_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('quest_id' => mt_rand(), 'person_id' => mt_rand());
		$linkage = new QuestPersonLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->quest_id == $quest_ids['quest_id'] &&
			$linkage->person_id == $quest_ids['person_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestPersonLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->quest_id == $this->defaultObject['quest_id'] && 
				$linkage->person_id == $this->defaultObject['person_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			person_id - {$linkage->person_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestPersonLinkageDAO($this->db, $this->defaultParams);
		$linkage->quest_id = 'foo';
		$linkage->person_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->defaultObject['quest_id'] && 
				$linkage->person_id != $this->defaultObject['person_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			person_id - {$linkage->person_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestPersonLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->quest_id) && 
				empty($linkage->person_id) &&
				empty($linkage->id));

		$error = "
			quest_id - {$linkage->quest_id}
			person_id - {$linkage->person_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
