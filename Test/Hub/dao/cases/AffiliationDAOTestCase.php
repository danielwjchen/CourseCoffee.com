<?php

require_once __DIR__ . '/../bootstrap.php';

class AffiliationDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$affiliation = new AffiliationDAO($this->db, $this->params);
		$result = ($affiliation->id == $this->record['id']);
		$error = print_r($affiliation, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'name' => 'Michigan State University',
			'url' => mt_rand(),
			'type' => $this->params['type']
		);

		$affiliation = new AffiliationDAO($this->db);
		$affiliation->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				a.*,
				t.name AS type,
				t.id AS type_id
			FROM `affiliation` a
			INNER JOIN `affiliation_type` t
					ON a.type_id = t.id
				WHERE a.name = :name",
			array('name' => $params['name'])
		);

		$result = ($new_record['name'] == $params['name']);
		$error = print_r($affiliation, true) . print_r($params, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$affiliation = new AffiliationDAO($this->db);
		$affiliation->read($this->record);
		$result = ($affiliation->id == $this->record['id']);
		$error = print_r($affiliation, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$affiliation = new AffiliationDAO($this->db);
		$affiliation->read($this->record);
		$affiliation->name = 'asdfsadf';
		$affiliation->update();

		$record = $this->db->fetch(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			($affiliation->id == $this->record['id']) &&
			($affiliation->name != $this->record['name']) &&
			($record['id'] == $this->record['id']) &&
			($record['name'] != $this->record['name'])
		);
		$error = print_r($affiliation, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$affiliation = new AffiliationDAO($this->db, $this->params);
		$affiliation->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM affiliation WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (empty($affiliation->id) && empty($record));
		$error = print_r($affiliation, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
