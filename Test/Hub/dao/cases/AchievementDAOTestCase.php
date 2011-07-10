<?php

require_once __DIR__ . '/../bootstrap.php';

class AchievementDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$achievement = new AchievementDAO($this->db, $this->params);
		$result = ($achievement->id == $this->record['id']);
		$error = print_r($achievement, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'type' => $this->params['type'],
			'name' => 'foo',
			'metric' => mt_rand(0, 100),
		);

		$achievement = new AchievementDAO($this->db);
		$achievement->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				a.*,
				t.name AS type,
				t.id AS type_id
			FROM `achievement` a
			INNER JOIN `achievement_type` t
				ON a.type_id = t.id
			WHERE a.name = :name",
			array('name' => $params['name'])
		);

		$result = ($new_record['name'] == $params['name']);
		$error = print_r($new_record, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$achievement = new AchievementDAO($this->db);
		$achievement->read($this->record);
		$result = ($achievement->id == $this->record['id']);
		$error = print_r($achievement, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$achievement = new AchievementDAO($this->db);
		$achievement->read($this->record);
		$achievement->name = 'asdfsadfasdfasdfasd';
		$achievement->update();
		$record = $this->db->fetch(
			'SELECT * FROM achievement WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		
		$result = (
			($achievement->id == $this->record['id']) &&
			($achievement->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);

		$error = print_r($achievement, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$achievement = new AchievementDAO($this->db, $this->params);
		$achievement->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM achievement WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($achievement->id) && empty($record));
		$error = print_r($achievement, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
