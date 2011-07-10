<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestItemLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestItemLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->item_id == $this->record['item_id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('quest_id' => mt_rand(), 'item_id' => mt_rand());
		$linkage = new QuestItemLinkageDAO($this->db);
		$linkage->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM `quest_item_linkage` 
			WHERE quest_id = :quest_id 
			AND item_id = :item_id", 
			$params
		);

		$result = (
			$new_record['quest_id'] == $params['quest_id'] &&
			$new_record['item_id'] == $params['item_id']
		);

		$error = print_r($params, true) . "\n" . print_r($new_record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestItemLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = (
			$linkage->quest_id == $this->record['quest_id'] && 
			$linkage->item_id == $this->record['item_id'] && 
			$linkage->id == $this->record['id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestItemLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 111;
		$linkage->item_id = 222;
		$linkage->update();

		$record = $this->db->fetch(
			'SELECT * FROM quest_item_linkage WHERE id = :id',
			array('id' => $this->record['id'])
		);

		$result = (
			($linkage->id == $this->record['id']) &&
			($linkage->quest_id != $this->record['quest_id']) &&
			($linkage->item_id != $this->record['item_id']) &&
			($record['id'] = $linkage->id) &&
			($record['quest_id'] != $this->record['quest_id']) &&
			($record['item_id'] != $this->record['item_id'])
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestItemLinkageDAO($this->db, $this->params);
		$linkage->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM quest_item_linkage WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			empty($linkage->quest_id) && 
			empty($linkage->item_id) &&
			empty($linkage->id) &&
			empty($record)
		);

		$error = print_r($record, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
