<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestLocationLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->quest_id == $this->record['quest_id'] &&
			$linkage->location_id == $this->record['location_id']);

		$error = "
			id - {$linkage->id}
			parent - {$linkage->quest_id}
			child - {$linkage->location_id}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
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
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new QuestLocationLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = ($linkage->quest_id == $this->record['quest_id'] && 
				$linkage->location_id == $this->record['location_id'] && 
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			location_id - {$linkage->location_id}
		".print_r($this->record, true);

		$this->assertTrue($result, 'User Creation');

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->params);
		$linkage->quest_id = 'foo';
		$linkage->location_id = 'bar';
		$linkage->update();
		$result = (($linkage->quest_id != $this->record['quest_id'] && 
				$linkage->location_id != $this->record['location_id']) &&
				$linkage->id == $this->record['id']);

		$error = "
			id - {$linkage->id}
			quest_id - {$linkage->quest_id}
			location_id - {$linkage->location_id}
		".print_r($this->record, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new QuestLocationLinkageDAO($this->db, $this->params);

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
