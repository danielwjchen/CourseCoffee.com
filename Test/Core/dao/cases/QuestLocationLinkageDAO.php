<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestLocationLinkageDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestLocationLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `quest_location_linkage`');
		$this->defaultParams = array(
			'quest_id' => mt_rand(),
			'location_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `quest_location_linkage` (quest_id, location_id) 
				VALUES (:quest_id, :location_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `quest_location_linkage` 
			WHERE quest_id = :quest_id AND location_id = :location_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `quest_location_linkage`');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->quest_id == $this->defaultObject['quest_id'] &&
			$linkage->location_id == $this->defaultObject['location_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->quest_id}
			child - {$linkage->location_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('quest_id' => mt_rand(), 'location_id' => mt_rand());
		$linkage = new QuestLocationLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->quest_id == $quest_ids['quest_id'] &&
			$linkage->location_id == $quest_ids['location_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestLocationLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->quest_id == $this->defaultObject['quest_id'] && 
				$linkage->location_id == $this->defaultObject['location_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			location_id - {$linkage->location_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->defaultParams);
		$linkage->quest_id = 'foo';
		$linkage->location_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->defaultObject['quest_id'] && 
				$linkage->location_id != $this->defaultObject['location_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			location_id - {$linkage->location_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->quest_id) && 
				empty($linkage->location_id) &&
				empty($linkage->id));

		$error = "
			quest_id - {$linkage->quest_id}
			location_id - {$linkage->location_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
