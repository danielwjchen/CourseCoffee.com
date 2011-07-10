<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestDateLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->date_id == $this->record['date_id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('quest_id' => mt_rand(), 'date_id' => mt_rand());
		$linkage = new QuestDateLinkageDAO($this->db);
		$linkage->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM `quest_date_linkage` 
			WHERE quest_id = :quest_id 
			AND date_id = :date_id", 
			$params
		);

		$result = (
			$new_record['quest_id'] == $params['quest_id'] &&
			$new_record['date_id'] == $params['date_id']
		);

		$error = print_r($params, true) . "\n" . print_r($new_record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestDateLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = (
			$linkage->quest_id == $this->record['quest_id'] && 
			$linkage->date_id == $this->record['date_id'] && 
			$linkage->id == $this->record['id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 'foo';
		$linkage->date_id = 'bar';
		$linkage->update();

		$record = $this->db->fetch(
			'SELECT * FROM quest_date_linkage WHERE id = :id',
			array('id' => $this->record['id'])
		);

		$result = (
			($linkage->id == $this->record['id']) &&
			($linkage->quest_id != $this->record['quest_id']) &&
			($linkage->date_id != $this->record['date_id']) &&
			($record['id'] = $linkage->id) &&
			($record['quest_id'] != $this->record['quest_id']) &&
			($record['date_id'] != $this->record['date_id'])
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestDateLinkageDAO($this->db, $this->params);
		$linkage->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM quest_date_linkage WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			empty($linkage->quest_id) && 
			empty($linkage->date_id) &&
			empty($linkage->id) &&
			empty($record)
		);

		$error = print_r($record, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
