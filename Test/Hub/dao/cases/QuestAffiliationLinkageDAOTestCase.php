<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestAffiliationLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->affiliation_id == $this->record['affiliation_id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('quest_id' => mt_rand(), 'affiliation_id' => mt_rand());
		$linkage = new QuestAffiliationLinkageDAO($this->db);
		$linkage->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM `quest_affiliation_linkage` 
			WHERE quest_id = :quest_id 
			AND affiliation_id = :affiliation_id", 
			$params
		);

		$result = (
			$new_record['quest_id'] == $params['quest_id'] &&
			$new_record['affiliation_id'] == $params['affiliation_id']
		);

		$error = print_r($params, true) . "\n" . print_r($new_record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestAffiliationLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = (
			$linkage->quest_id == $this->record['quest_id'] && 
			$linkage->affiliation_id == $this->record['affiliation_id'] && 
			$linkage->id == $this->record['id']
		);

		$error = print_r($linkage->attribute, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 1111;
		$linkage->affiliation_id = 222;
		$linkage->update();

		$record = $this->db->fetch(
			'SELECT * FROM quest_affiliation_linkage WHERE id = :id',
			array('id' => $this->record['id'])
		);

		$result = (
			($linkage->id == $this->record['id']) &&
			($record['id'] = $linkage->id) &&
			($record['quest_id'] != $this->record['quest_id']) &&
			($record['affiliation_id'] != $this->record['affiliation_id'])
		);

		$error = 
			'object - ' . print_r($linkage->attribute, true) . "\n" . 
			'record - ' . print_r($record, true) . "\n" . 
			'params - ' . print_r($this->params, true) . "\n"
		;

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestAffiliationLinkageDAO($this->db, $this->params);
		$linkage->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM quest_affiliation_linkage WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			empty($linkage->quest_id) && 
			empty($linkage->affiliation_id) &&
			empty($linkage->id) &&
			empty($record)
		);

		$error = print_r($record, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
