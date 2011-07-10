<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestMessageLinkageDAOTestCase extends DAOTestCase implements DAOTestInterface{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestMessageLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `quest_message_linkage`');
		$this->defaultParams = array(
			'quest_id' => mt_rand(),
			'message_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `quest_message_linkage` (quest_id, message_id) 
				VALUES (:quest_id, :message_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `quest_message_linkage` 
			WHERE quest_id = :quest_id AND message_id = :message_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `quest_message_linkage`');
	}

	/**
	 * Implement DAOTestInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestMessageLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->quest_id == $this->defaultObject['quest_id'] &&
			$linkage->message_id == $this->defaultObject['message_id']);

		$error = "
			id - {$linkage->id}
			quest - {$linkage->quest_id}
			message - {$linkage->message_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('quest_id' => mt_rand(), 'message_id' => mt_rand());
		$linkage = new QuestMessageLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->quest_id == $quest_ids['quest_id'] &&
			$linkage->message_id == $quest_ids['message_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestMessageLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->quest_id == $this->defaultObject['quest_id'] && 
				$linkage->message_id == $this->defaultObject['message_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			message_id - {$linkage->message_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestMessageLinkageDAO($this->db, $this->defaultParams);
		$linkage->quest_id = 'foo';
		$linkage->message_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->defaultObject['quest_id'] && 
				$linkage->message_id != $this->defaultObject['message_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			message_id - {$linkage->message_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestMessageLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->quest_id) && 
				empty($linkage->message_id) &&
				empty($linkage->id));

		$error = "
			quest_id - {$linkage->quest_id}
			message_id - {$linkage->message_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
