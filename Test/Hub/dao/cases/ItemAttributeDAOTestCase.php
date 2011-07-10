<?php

require_once __DIR__ . '/../bootstrap.php';

class ItemAttributeDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$item_attribute = new ItemAttributeDAO($this->db, $this->params);
		$result = ($item_attribute->id == $this->record['id']);
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'value' => 'foo bar',
			'item_id' => $this->params['item_id'],
			'type' => $this->params['type'],
		);

		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->create($params);
		$result = ($item_attribute->value == $params['value']);
		$error = print_r($item_attribute, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->read($this->params);
		$result = ($item_attribute->id == $this->record['id']);
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$item_attribute = new ItemAttributeDAO($this->db);
		$item_attribute->read($this->record);
		$item_attribute->value = 'asdfsadf';
		$item_attribute->update();
		$result = (($item_attribute->id == $this->record['id']) &&
			($item_attribute->value != $this->record['value']));
		$error = print_r($item_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$item_attribute = new ItemAttributeDAO($this->db, $this->params);
		$item_attribute->destroy();
		$record = $this->db->perform(
			'SELECT * FROM item_attribute WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($item_attribute->id) && empty($record));
		$error = print_r($item_attribute, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
