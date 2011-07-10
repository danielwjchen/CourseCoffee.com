<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestStatisticLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestStatisticLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->statistic_id == $this->record['statistic_id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('quest_id' => mt_rand(), 'statistic_id' => mt_rand());
		$linkage = new QuestStatisticLinkageDAO($this->db);
		$linkage->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM `quest_statistic_linkage` 
			WHERE quest_id = :quest_id 
			AND statistic_id = :statistic_id", 
			$params
		);

		$result = (
			$new_record['quest_id'] == $params['quest_id'] &&
			$new_record['statistic_id'] == $params['statistic_id']
		);

		$error = print_r($params, true) . "\n" . print_r($new_record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestStatisticLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = (
			$linkage->quest_id == $this->record['quest_id'] && 
			$linkage->statistic_id == $this->record['statistic_id'] && 
			$linkage->id == $this->record['id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestStatisticLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 111;
		$linkage->statistic_id = 333;
		$linkage->update();

		$record = $this->db->fetch(
			'SELECT * FROM quest_statistic_linkage WHERE id = :id',
			array('id' => $this->record['id'])
		);

		$result = (
			($linkage->id == $this->record['id']) &&
			($linkage->quest_id != $this->record['quest_id']) &&
			($linkage->statistic_id != $this->record['statistic_id']) &&
			($record['id'] = $linkage->id) &&
			($record['quest_id'] != $this->record['quest_id']) &&
			($record['statistic_id'] != $this->record['statistic_id'])
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestStatisticLinkageDAO($this->db, $this->params);
		$linkage->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM quest_statistic_linkage WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			empty($linkage->quest_id) && 
			empty($linkage->statistic_id) &&
			empty($linkage->id) &&
			empty($record)
		);

		$error = print_r($record, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
