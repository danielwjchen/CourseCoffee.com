<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeSectionDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_section = new CollegeSectionDAO($this->db, $this->params);
		$result = (
			$college_section->id == $this->record['id'] &&
			$college_section->section == $this->record['section'] &&
			$college_section->college_id == $this->record['college_id']
		);
		$error = print_r($college_section, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'section' => '001',
			'description' => 'Most awesome course to study in college',
			'college' => $this->params['college'],
			'subject' => $this->params['subject'],
			'course' => $this->params['course'],
		);

		$college_section = new CollegeSectionDAO($this->db);
		$college_section->create($params);

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
			WHERE q.objective = :section
				AND a.name = :college
				AND qt.name = :type
			", 
			array(
				'section' => $params['section'],
				'college' => $params['college'],
				'type' => 'college_section',
			)
		);

		$result = (
			$new_record['objective'] == $params['section'] &&
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
		$college_section = new CollegeSectionDAO($this->db);
		$college_section->read($this->params);
		$result = (
			$college_section->id == $this->record['id'] &&
			$college_section->section == $this->record['section']
		);
		$error = print_r($college_section->attribute, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_section = new CollegeSectionDAO($this->db);
		$college_section->read($this->params);
		$college_section->section = '003';
		$college_section->update();

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
			WHERE q.id = :id
			", 
			array('id' => $college_section->id)
		);

		$result = (
			($college_section->id == $this->record['id']) &&
			($new_record['id'] == $this->record['id']) &&
			($new_record['objective'] != $this->record['section'])
		);
		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'record - ' . print_r($this->record, true) . "\n" .
			'object - ' . print_r($college_section->attribute, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_section = new CollegeSectionDAO($this->db, $this->params);
		$college_section->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_section->id) && empty($record));
		$error = print_r($college_section, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
