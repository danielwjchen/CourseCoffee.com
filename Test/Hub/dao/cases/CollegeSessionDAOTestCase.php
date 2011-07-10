<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeSessionDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_session = new CollegeSessionDAO($this->db, $this->params);
		$result = (
			$college_session->id == $this->record['id'] &&
			$college_session->section == $this->record['section'] &&
			$college_session->college_id == $this->record['college_id']
		);
		$error = 
			'object - ' . print_r($college_session->attribute, true) . "\n" . 
			'record - ' . print_r($this->record, true) . "\n"
		;

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'college' => $this->params['college'],
			'subject' => $this->params['subject'],
			'course' => $this->params['course'],
			'section' => $this->params['section'],
			'session' => 'M_W',
			'type' => $this->params['type'],
			'description' => 'Class takes place on Monday and Wednesday',
		);

		$college_session = new CollegeSessionDAO($this->db);
		$college_session->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				q.*,
				qa. *
			FROM quest q
			INNER JOIN quest_affiliation_linkage qa_linkage
				ON qa_linkage.quest_id = q.id
			INNER JOIN affiliation a
				ON qa_linkage.affiliation_id = a.id
			INNER JOIN quest_type qt
				ON qt.id = q.type_id
			INNER JOIN quest_attribute qa
				ON qa.quest_id = q.id
			INNER JOIN quest_attribute qat
				ON qat.id = qa.type_id
			WHERE q.objective = :session
				AND a.name = :college
				AND qt.name = :type
			", 
			array(
				'session' => $params['session'],
				'college' => $params['college'],
				'type' => 'college_session',
			)
		);

		$result = (
			$new_record['objective'] == $params['session'] &&
			$new_record['value'] == $params['type'] &&
			$new_record['description']  == $params['description']
		);

		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'params - ' . print_r($params, true) . "\n"
		;

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testRead().
	 */
	function testRead() {
		$college_session = new CollegeSessionDAO($this->db);
		$college_session->read($this->params);
		$result = (
			$college_session->id == $this->record['id'] &&
			$college_session->section == $this->record['section']
		);
		$error = print_r($college_session->attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_session = new CollegeSessionDAO($this->db);
		$college_session->read($this->params);
		$college_session->session = 'Th_Tu';
		$college_session->update();

		$new_record = $this->db->fetch("
			SELECT 
				q.*
			FROM quest q
			INNER JOIN quest_affiliation_linkage qa_linkage
				ON qa_linkage.quest_id = q.id
			INNER JOIN affiliation a
				ON qa_linkage.affiliation_id = a.id
			INNER JOIN quest_type qt
				ON qt.id = q.type_id
			INNER JOIN quest_attribute qa
				ON qa.quest_id = q.id
			INNER JOIN quest_attribute qat
				ON qat.id = qa.type_id
			WHERE q.id = :id
			", 
			array('id' => $college_session->id)
		);

		$result = (
			($college_session->id == $this->record['id']) &&
			($new_record['id'] == $this->record['id']) &&
			($new_record['objective'] != $this->record['session'])
		);
		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'record - ' . print_r($this->record, true) . "\n" .
			'object - ' . print_r($college_session->attribute, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_session = new CollegeSessionDAO($this->db, $this->params);
		$college_session->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_session->id) && empty($record));
		$error = print_r($college_session, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
