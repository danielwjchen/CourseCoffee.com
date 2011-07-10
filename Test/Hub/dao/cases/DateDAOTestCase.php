<?php

require_once __DIR__ . '/../bootstrap.php';

class DateDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$date = new DateDAO($this->db, $this->params);
		$result = ($date->id == $this->record['id']);
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'timestamp' => mt_rand(0, time()),
			'type' => $this->params['type']
		);

		$date = new DateDAO($this->db);
		$date->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				d.*,
				dt.name AS type
			FROM `date` d
			INNER JOIN `date_type` dt
				ON d.type_id = dt.id
			WHERE	d.timestamp = :timestamp
				AND dt.name = :type", 
			array(
				'timestamp' => $params['timestamp'],
				'type' => $params['type']
			)
		);

		$result = ($new_record['timestamp'] == $params['timestamp']);
		$error = print_r($new_record, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$date = new DateDAO($this->db);
		$date->read($this->record);
		$result = ($date->id == $this->record['id']);
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$date = new DateDAO($this->db);
		$date->read($this->record);
		$date->timestamp = mt_rand(0, time());
		$date->update();
		$result = (($date->id == $this->record['id']) &&
			($date->timestamp != $this->record['timestamp']));
		$error = print_r($date, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$date = new DateDAO($this->db, $this->params);
		$date->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM date WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($date->id) && empty($record));
		$error = print_r($date, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
