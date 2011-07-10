<?php

require_once __DIR__ . '/../bootstrap.php';

class StatisticDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$statistic = new StatisticDAO($this->db, $this->params);
		$result = ($statistic->id == $this->record['id']);
		$error = print_r($statistic, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'type' => $this->params['type'],
			'data' => mt_rand(0, 100),
		);

		$statistic = new StatisticDAO($this->db);
		$statistic->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				s.*,
				st.name AS type,
				st.id AS type_id
			FROM `statistic` s
			INNER JOIN `statistic_type` st
				ON s.type_id = st.id
			WHERE s.data = :data",
			array('data' => $params['data'])
		);

		$result = ($new_record['data'] == $params['data']);
		$error = print_r($new_record, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$statistic = new StatisticDAO($this->db);
		$statistic->read($this->record);
		$result = ($statistic->id == $this->record['id']);
		$error = print_r($statistic, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$statistic = new StatisticDAO($this->db);
		$statistic->read($this->record);
		$statistic->data = mt_rand(100, 200000);
		$statistic->update();
		$record = $this->db->fetch(
			'SELECT * FROM statistic WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		
		$result = (
			($statistic->id == $this->record['id']) &&
			($statistic->data != $this->record['data']) &&
			($record['id'] == $this->record['id']) &&
			($record['data'] != $this->record['data'])
		);

		$error = print_r($statistic, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$statistic = new StatisticDAO($this->db, $this->params);
		$statistic->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM statistic WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($statistic->id) && empty($record));
		$error = print_r($statistic, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
