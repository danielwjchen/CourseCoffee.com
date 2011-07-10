<?php

require_once __DIR__ . '/../bootstrap.php';

class QuestAttributeDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$quest_attribute = new QuestAttributeDAO($this->db, $this->params);
		$result = ($quest_attribute->id == $this->record['id']);
		$error = print_r($quest_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'value' => 'foo bar',
			'quest_id' => $this->params['quest_id'],
			'type' => $this->params['type'],
		);

		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->create($params);

		$new_record = $this->db->fetch("
			SELECT qa.*
				FROM quest_attribute qa
			INNER JOIN quest_attribute_type qat
				ON qa.type_id = qat.id
			WHERE	qa.value = :value
				AND qat.name = :type
			",
			array(
				'value' => $params['value'],
				'type' => $params['type']
			)
		);
		$result = ($new_record['value'] == $params['value']);
		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'params - ' . print_r($params, true) ."\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->read($this->params);
		$result = ($quest_attribute->id == $this->record['id']);
		$error = print_r($quest_attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$quest_attribute = new QuestAttributeDAO($this->db);
		$quest_attribute->read($this->record);
		$quest_attribute->value = 'asdfsadf';
		$quest_attribute->update();
		$new_record = $this->db->fetch("
			SELECT qa.*
				FROM quest_attribute qa
			INNER JOIN quest_attribute_type qat
				ON qa.type_id = qat.id
			WHERE	qa.id = :id
			",
			array(
				'id' => $quest_attribute->id
			)
		);

		$result = (
			($new_record['id'] == $this->record['id']) &&
			($new_record['value'] != $this->record['value'])
		);

		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'old record - ' . print_r($this->record, true) ."\n"
		;
		
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$quest_attribute = new QuestAttributeDAO($this->db, $this->params);
		$quest_attribute->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest_attribute WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($quest_attribute->id) && empty($record));
		$error = print_r($quest_attribute, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
