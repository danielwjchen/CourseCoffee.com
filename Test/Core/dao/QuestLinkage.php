<?php

require_once __DIR__ . '/DAOTestCase.php';

class QuestLinkageDAOTestCase extends DAOTestCase implements DAOTestTemplate{

	protected $defaultObject;
	protected $defaultParams;

	/**
	 * Implement DAOTestCase::__construct()
	 */
	function __construct() {
		parent::__construct();
		require_once DAO_PATH .'QuestLinkageDAO.php';

	}

	/**
	 * Set up test case.
	 */
	function setUp() {
		$this->db->perform('TRUNCATE TABLE `quest_linkage`');
		$this->defaultParams = array(
			'parent_id' => mt_rand(),
			'child_id' => mt_rand()
		);

		$this->db->perform(
			"INSERT INTO `quest_linkage` (parent_id, child_id) 
				VALUES (:parent_id, :child_id)",
			$this->defaultParams);

		$this->defaultObject = $this->db->fetch(
			"SELECT * FROM `quest_linkage` 
			WHERE parent_id = :parent_id AND child_id = :child_id", 
			$this->defaultParams);
	}

	/**
	 * Tear down test case.
	 */
	function tearDown() {
		$this->db->perform('TRUNCATE TABLE `quest_linkage`');
	}

	/**
	 * Implement DAOTestTemplate::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestLinkageDAO($this->db, $this->defaultParams);

		$result = ($linkage->id == $this->defaultObject['id'] &&
			$linkage->parent_id == $this->defaultObject['parent_id'] &&
			$linkage->child_id == $this->defaultObject['child_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->parent_id}
			child - {$linkage->child_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('parent_id' => mt_rand(), 'child_id' => mt_rand());
		$linkage = new QuestLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->parent_id == $quest_ids['parent_id'] &&
			$linkage->child_id == $quest_ids['child_id']);

		$error = print_r($quest_ids, true) . "\n" . print_r($linkage, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestTemplate::testRead().
	 */
	function testRead() {
		$linkage = new QuestLinkageDAO($this->db);
		$linkage->read($this->defaultObject);

		$result = ($linkage->parent_id == $this->defaultObject['parent_id'] && 
				$linkage->child_id == $this->defaultObject['child_id'] && 
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			parent_id - {$linkage->parent_id}
			child_id - {$linkage->child_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestTemplate::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestLinkageDAO($this->db, $this->defaultParams);
		$linkage->parent_id = 'foo';
		$linkage->child_id = 'bar';
		$linkage->update();
		$result = (($linkage->parent_id != $this->defaultObject['parent_id'] && 
				$linkage->child_id != $this->defaultObject['child_id']) &&
				$linkage->id == $this->defaultObject['id']);

		$error = "
			id - {$linkage->id}
			parent_id - {$linkage->parent_id}
			child_id - {$linkage->child_id}
		".print_r($this->defaultObject, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestTemplate::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestLinkageDAO($this->db, $this->defaultParams);

		$result = (empty($linkage->parent_id) && 
				empty($linkage->child_id) &&
				empty($linkage->id));

		$error = "
			parent_id - {$linkage->parent_id}
			child_id - {$linkage->child_id}
			id - {$linkage->id}
		";

		$this->assertTrue($result, $error);

	}

}
