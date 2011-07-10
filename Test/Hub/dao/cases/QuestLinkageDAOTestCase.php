<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->parent_id == $this->record['parent_id'] &&
			$linkage->child_id == $this->record['child_id']);

		$error = print_r($linkage, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$quest_ids = array('parent_id' => mt_rand(), 'child_id' => mt_rand());
		$linkage = new QuestLinkageDAO($this->db);
		$linkage->create($quest_ids);
		$result = ($linkage->parent_id == $quest_ids['parent_id'] &&
			$linkage->child_id == $quest_ids['child_id']);

		$error = print_r($linkage, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = ($linkage->parent_id == $this->record['parent_id'] && 
				$linkage->child_id == $this->record['child_id'] && 
				$linkage->id == $this->record['id']);

		$error = print_r($linkage, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestLinkageDAO($this->db, $this->params);
		$linkage->parent_id = 'foo';
		$linkage->child_id = 'bar';
		$linkage->update();
		$result = (($linkage->parent_id != $this->record['parent_id'] && 
				$linkage->child_id != $this->record['child_id']) &&
				$linkage->id == $this->record['id']);

		$error = print_r($linkage, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestLinkageDAO($this->db, $this->params);

		$result = (empty($linkage->parent_id) && 
				empty($linkage->child_id) &&
				empty($linkage->id));

		$error = print_r($linkage, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
