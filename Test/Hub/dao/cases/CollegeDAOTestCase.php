<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college = new CollegeDAO($this->db, $this->params);
		$result = ($college->id == $this->record['id']);
		$error = print_r($college, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Michigan State University',
			'url' => mt_rand(),
		);

		$college = new CollegeDAO($this->db);
		$college->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				a.*,
				t.name AS type,
				t.id AS type_id
			FROM `affiliation` a
			INNER JOIN `affiliation_type` t
					ON a.type_id = t.id
			WHERE a.name = :name
				AND t.name = 'college'",
			array('name' => $params['name'])
		);

		$result = ($new_record['name'] == $params['name']);
		$error = print_r($college, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$college = new CollegeDAO($this->db);
		$college->read($this->record);
		$result = ($college->id == $this->record['id']);
		$error = print_r($college, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college = new CollegeDAO($this->db);
		$college->read($this->record);
		$college->name = 'asdfsadf';
		$college->update();

		$record = $this->db->fetch(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($college->id == $this->record['id']) &&
			($college->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);
		$error = print_r($college, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college = new CollegeDAO($this->db, $this->params);
		$college->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college->id) && empty($record));
		$error = print_r($college, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
