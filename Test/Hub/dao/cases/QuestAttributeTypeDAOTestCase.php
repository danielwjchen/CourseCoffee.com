<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestAttributeTypeDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$type = new QuestAttributeTypeDAO($this->db, $this->params);

		$result = ($type->id == $this->record['id'] &&
			$type->name == $this->record['name']);

		$error = "
			id - {$type->id}
			type - {$type->name}
		";

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('name' => mt_rand(4,5));
		$type = new QuestAttributeTypeDAO($this->db);
		$type->create($params);

		$new_record = $this->db->fetch(
			"SELECT * FROM `quest_attribute_type` WHERE `name` = :name",
			array('name' => $params['name'])
		);

		$result = ($new_record['name'] == $params['name']);
		$error = print_r($params, true) . "\n" . print_r($type, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$type = new QuestAttributeTypeDAO($this->db);
		$type->read($this->record);

		$result = ($type->name == $this->record['name'] && 
				$type->id == $this->record['id']);

		$error = print_r($type, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$type = new QuestAttributeTypeDAO($this->db, $this->params);
		$type->name = 'foo bar';
		$type->update();

		$record = $this->db->fetch(
			'SELECT * FROM quest_attribute_type WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($type->id == $this->record['id']) &&
			($type->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);

		$error = print_r($type, true) . print_r($record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$type = new QuestAttributeTypeDAO($this->db, $this->params);
		$type->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest_attribute_type WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (empty($type->id) && empty($record));
		$error = print_r($type, true) . print_r($record, true);
		$this->assertTrue($result, $error);

	}

}
