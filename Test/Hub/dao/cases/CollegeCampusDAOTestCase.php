<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeCampusDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_campus = new CollegeCampusDAO($this->db, $this->params);
		$result = ($college_campus->id == $this->record['id']);
		$error = print_r($college_campus, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Lake Oswego',
			'longitude' => mt_rand(),
			'latitude' => mt_rand(),
			'college' => $this->record['college'],
		);

		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->create($params);
		$new_record  = $this->db->fetch("
			SELECT 
				l.*,
				lt.name AS type,
				lt.id AS type_id
			FROM `location` l
			INNER JOIN `location_type` lt
				ON l.type_id = lt.id
			WHERE	l.name = :name", 
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
		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->read($this->record);
		$result = ($college_campus->id == $this->record['id']);
		$error = print_r($college_campus, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_campus = new CollegeCampusDAO($this->db);
		$college_campus->read($this->record);
		$college_campus->name = 'asdfsadf';
		$college_campus->update();
		$record = $this->db->fetch(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($college_campus->id == $this->record['id']) &&
			($college_campus->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);

		$error = print_r($college_campus, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_campus = new CollegeCampusDAO($this->db, $this->params);
		$college_campus->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_campus->id) && empty($record));
		$error = print_r($college_campus, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
