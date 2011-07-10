<?php

require_once __DIR__ . '/../bootstrap.php';

class ItemDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$item = new ItemDAO($this->db, $this->params);
		$result = ($item->id == $this->record['id']);
		$error = print_r($item, true) . print_r($this->record, true);
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
			'type' => $this->params['type'],
		);

		$item = new ItemDAO($this->db);
		$item->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				i.*,
				t.name AS type,
				t.id AS type_id
			FROM `item` i
			INNER JOIN `item_type` t
				ON i.type_id = t.id
			WHERE i.name = :name",
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
		$item = new ItemDAO($this->db);
		$item->read($this->record);
		$result = ($item->id == $this->record['id']);
		$error = print_r($item, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$item = new ItemDAO($this->db);
		$item->read($this->record);
		$item->name = 'asdfsadf';
		$item->update();
		$record = $this->db->fetch(
			'SELECT * FROM item WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($item->id == $this->record['id']) &&
			($item->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);

		$error = print_r($item, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$item = new ItemDAO($this->db, $this->params);
		$item->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM item WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($item->id) && empty($record));
		$error = print_r($item, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
