<?php

require_once __DIR__ . '/../bootstrap.php';

class LocationDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$location = new LocationDAO($this->db, $this->params);
		$result = ($location->id == $this->record['id']);
		$error = print_r($location, true) . print_r($this->record, true);
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
			'type' => $this->params['type']
		);

		$location = new LocationDAO($this->db);
		$location->create($params);
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
		$location = new LocationDAO($this->db);
		$location->read($this->record);
		$result = ($location->id == $this->record['id']);
		$error = print_r($location, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$location = new LocationDAO($this->db);
		$location->read($this->record);
		$location->name = 'asdfsadf';
		$location->update();
		$record = $this->db->fetch(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($location->id == $this->record['id']) &&
			($location->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);

		$error = print_r($location, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$location = new LocationDAO($this->db, $this->params);
		$location->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM location WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($location->id) && empty($record));
		$error = print_r($location, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
