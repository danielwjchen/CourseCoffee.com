<?php

require_once __DIR__ . '/../bootstrap.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';

class QuestDateRangeDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$quest_date_range = new QuestDateRangeDAO($this->db, $this->params);
		$result = ($quest_date_range->quest_id == $this->record['quest_id']);
		$error = print_r($quest_date_range, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
			),
		));

		$params = array(
			'quest_id' => $quest['record']['id'],
			'begin_date' => mt_rand(time() - 10000, time() + 1000),
			'end_date' => mt_rand(time() + 10000, time() + 20000),
		);

		$quest_date_range = new QuestDateRangeDAO($this->db);
		$quest_date_range->create($params);
		$sql = "
			SELECT
				linkage.quest_id AS quest_id,
				d.timestamp
			FROM quest_date_linkage linkage
			INNER JOIN date d
				ON linkage.date_id = d.id
			INNER JOIN date_type dt
				ON d.type_id = dt.id
			WHERE	linkage.quest_id = :quest_id
				AND dt.name = :type
			";

		$new_begin_date = $this->db->fetch($sql, array(
			'quest_id' => $params['quest_id'],
			'type' => 'begin_date',
		));

		$new_end_date = $this->db->fetch($sql, array(
			'quest_id' => $params['quest_id'],
			'type' => 'end_date',
		));

		$result = (
			$new_begin_date['timestamp'] == $params['begin_date'] &&
			$new_end_date['timestamp'] == $params['end_date']
		);

		$error = 
			print_r($new_end_date, true) . 
			print_r($new_begin_date, true) . 
			print_r($params, true)
		;

		$this->assertTrue($result, $error);
		$quest_date_range = QuestDAOSetup::CleanUp();
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$quest_date_range = new QuestDateRangeDAO($this->db);
		$quest_date_range->read($this->record);
		$result = ($quest_date_range->quest_id == $this->record['quest_id']);
		$error = print_r($quest_date_range, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$quest_date_range = new QuestDateRangeDAO($this->db);
		$quest_date_range->read($this->record);
		$quest_date_range->end_date = mt_rand(time() + 30000, time() + 40000);
		$quest_date_range->update();
		$result = (($quest_date_range->quest_id == $this->record['quest_id']) &&
			($quest_date_range->end_date != $this->record['end_date']));
		$error = print_r($quest_date_range, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$quest_date_range = new QuestDateRangeDAO($this->db, $this->params);
		$quest_date_range->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest_date_linkage WHERE quest_id = :quest_id', 
			array('quest_id' => $this->record['quest_id'])
		);
		$result = (empty($quest_date_range->quest_id) && empty($record));
		$error = print_r($quest_date_range, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
