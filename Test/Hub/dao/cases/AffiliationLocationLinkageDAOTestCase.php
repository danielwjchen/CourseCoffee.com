<?php

require_once __DIR__ . '/../bootstrap.php';

class AffiliationLocationLinkageDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);

		$result = ($linkage->id == $this->record['id'] &&
			$linkage->affiliation_id == $this->record['affiliation_id'] &&
			$linkage->location_id == $this->record['location_id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array('affiliation_id' => mt_rand(), 'location_id' => mt_rand());
		$linkage = new AffiliationLocationLinkageDAO($this->db);
		$linkage->create($params);

		$new_record = $this->db->fetch("
			SELECT * FROM `affiliation_location_linkage` 
			WHERE affiliation_id = :affiliation_id 
			AND location_id = :location_id", 
			$params
		);

		$result = (
			$new_record['affiliation_id'] == $params['affiliation_id'] &&
			$new_record['location_id'] == $params['location_id']
		);

		$error = print_r($params, true) . "\n" . print_r($new_record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$linkage = new AffiliationLocationLinkageDAO($this->db);
		$linkage->read($this->record);

		$result = (
			$linkage->affiliation_id == $this->record['affiliation_id'] && 
			$linkage->location_id == $this->record['location_id'] && 
			$linkage->id == $this->record['id']
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);
		$linkage->affiliation_id = 11111;
		$linkage->location_id = 222222;
		$linkage->update();
		$record = $this->db->fetch(
			'SELECT * FROM affiliation_location_linkage WHERE id = :id',
			array('id' => $this->record['id'])
		);

		$result = (
			($linkage->id == $this->record['id']) &&
			($linkage->affiliation_id != $this->record['affiliation_id']) &&
			($linkage->location_id != $this->record['location_id']) &&
			($record['id'] = $linkage->id) &&
			($record['affiliation_id'] != $this->record['affiliation_id']) &&
			($record['location_id'] != $this->record['location_id'])
		);

		$error = print_r($linkage, true) . print_r($this->params, true);

		$this->assertTrue($result, $error);

	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$linkage = new AffiliationLocationLinkageDAO($this->db, $this->params);
		$linkage->destroy();

		$record = $this->db->fetch(
			'SELECT * FROM affiliation_location_linkage WHERE id = :id', 
			array('id' => $this->record['id'])
		);

		$result = (
			empty($linkage->affiliation_id) && 
			empty($linkage->location_id) &&
			empty($linkage->id) &&
			empty($record)
		);

		$error = print_r($record, true) . print_r($this->params, true);
		$this->assertTrue($result, $error);

	}

}
