<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeSemesterDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_semester = new CollegeSemesterDAO($this->db, $this->params);
		$result = ($college_semester->id == $this->record['id']);
		$error = print_r($college_semester, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'college' => $this->record['college'],
			'name' => "Spring 11",
			'description' => 'foo bar',
			'begin_date' => mt_rand(time() - 1000, time() + 1000),
			'end_date' => mt_rand(time() +1000, time() + 2000),
		);

		$college_semester = new CollegeSemesterDAO($this->db);
		$college_semester->create($params);
		$new_college_semester = $this->db->fetch("
			SELECT 
				q.id AS id,
				q.objective AS name,
				q.description AS description
			FROM quest q
			WHERE q.objective = :name
			",
			array('name' => $params['name'])
		);

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
			'quest_id' => $new_college_semester['id'],
			'type' => 'begin_date',
		));

		$new_end_date = $this->db->fetch($sql, array(
			'quest_id' => $new_college_semester['id'],
			'type' => 'end_date',
		));

		$result = (
			$new_college_semester['name'] == $params['name'] &&
			$new_begin_date['timestamp'] == $params['begin_date'] &&
			$new_end_date['timestamp'] == $params['end_date']
		);
		$error = 
			'new_college_semester - ' . print_r($new_college_semester, true) . "\n" . 
			'new_begin_date - ' . print_r($new_begin_date, true) . "\n".
			'new_end_date - ' . print_r($new_end_date, true) . "\n" .
			'params - ' . print_r($params, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$college_semester = new CollegeSemesterDAO($this->db);
		$college_semester->read($this->record);
		$result = ($college_semester->id == $this->record['id']);
		$error = print_r($college_semester, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_semester = new CollegeSemesterDAO($this->db);
		$college_semester->read($this->record);
		$college_semester->name = 'asdfsadf';
		$college_semester->update();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($college_semester->id == $this->record['id']) &&
			($college_semester->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['objective'] != $this->record['name'])
		);

		$error = 
			'college_semester' . print_r($college_semester, true) . "\n" . 
			'updated record' . print_r($record, true) . "\n" . 
			'record' . print_r($this->record, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_semester = new CollegeSemesterDAO($this->db, $this->params);
		$college_semester->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_semester->id) && empty($record));
		$error = print_r($college_semester, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
