<?php

require_once dirname(__FILE__) . '/DAOTestCase.php';

class QuestDateLinkageDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestDateLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `quest_date_linkage`');
		$this->defaultParams = array(
			'quest_id' => mt_rand(),
			'date_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `quest_date_linkage` (quest_id, date_id) 
				VALUES (:quest_id, :date_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `quest_date_linkage` 
			WHERE quest_id = :quest_id AND date_id = :date_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `quest_date_linkage`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->quest_id == $this->defaultObject['quest_id'] &&
			$linkage->date_id == $this->defaultObject['date_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->quest_id}
			child - {$linkage->date_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('quest_id' => mt_rand(), 'date_id' => mt_rand());
		$linkage = new QuestDateLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->quest_id == $quest_ids['quest_id'] &&
			$linkage->date_id == $quest_ids['date_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$linkage = new QuestDateLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->quest_id == $this->defaultObject['quest_id'] && 
				$linkage->date_id == $this->defaultObject['date_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			date_id - {$linkage->date_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->defaultParams);
		$linkage->quest_id = 'foo';
		$linkage->date_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->defaultObject['quest_id'] && 
				$linkage->date_id != $this->defaultObject['date_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			date_id - {$linkage->date_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->quest_id) && 
				empty($linkage->date_id) &&
				empty($linkage->id));

		$error = "
			quest_id - {$linkage->quest_id}
			date_id - {$linkage->date_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
