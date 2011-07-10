<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$quest = new QuestDAO($this->db, $this->params);
		$result = ($quest->id == $this->record['id']);
		$error = print_r($quest, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'type' => $this->params['type'],
			'user_id' => $this->params['user_id'],
			'objective' => 'foo',
			'description' => 'bar',
		);

		$quest = new QuestDAO($this->db);
		$quest->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				q.*,
				t.name AS type,
				t.id AS type_id
			FROM `quest` q
			INNER JOIN `quest_type` t
				ON q.type_id = t.id
			INNER JOIN `user` u
				ON q.user_id = u.id
			WHERE q.objective = :objective",
			array('objective' => $params['objective'])
		);

		$result = ($new_record['objective'] == $params['objective']);
		$error = print_r($new_record, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$quest = new QuestDAO($this->db);
		$quest->read($this->record);
		$result = ($quest->id == $this->record['id']);
		$error = print_r($quest, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$quest = new QuestDAO($this->db);
		$quest->read($this->record);
		$quest->objective = 'asdfsadfasdfasdfasd';
		$quest->update();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		
		$result = (
			($quest->id == $this->record['id']) &&
			($quest->objective != $this->record['objective']) &&
			($record['id'] == $this->record['id']) &&
			($record['objective'] != $this->record['objective'])
		);

		$error = print_r($quest, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$quest = new QuestDAO($this->db, $this->params);
		$quest->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($quest->id) && empty($record));
		$error = print_r($quest, true) . print_r($record, true);
		$this->assertTrue($result, $error);
	}

}
