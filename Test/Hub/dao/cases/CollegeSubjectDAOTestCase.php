<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeSubjectDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_subject = new CollegeSubjectDAO($this->db, $this->params);
		$result = ($college_subject->id == $this->record['id']);
		$error = print_r($college_subject, true) . print_r($this->record, true);

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'subject' => 'Computer Science and Engineering',
			'description' => 'Most awesome subject to study in college',
			'college' => $this->params['college'],
			'abbr' => 'CSE',
		);

		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->create($params);

		$new_record = $this->db->fetch("
			SELECT 
				q.*,
				qa.value
			FROM quest q
			INNER JOIN quest_affiliation_linkage qa_linkage
				ON qa_linkage.quest_id = q.id
			INNER JOIN affiliation a
				ON qa_linkage.affiliation_id = a.id
			INNER JOIN quest_attribute qa
				ON qa.quest_id = q.id
			INNER JOIN quest_attribute_type qat
				ON qat.id = qa.type_id
			WHERE q.objective = :subject
				AND a.name = :college
				AND qat.name = :type
			", 
			array(
				'subject' => $params['subject'],
				'college' => $params['college'],
				'type' => 'college_subject_abbr',
			)
		);

		$result = (
			$new_record['objective'] == $params['subject'] &&
			$new_record['value']  == $params['abbr']
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
		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->read($this->params);
		$result = ($college_subject->id == $this->record['id']);
		$error = print_r($college_subject, true) . print_r($this->record, true);
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_subject = new CollegeSubjectDAO($this->db);
		$college_subject->read($this->record);
		$college_subject->abbr = 'asdfsadf';
		$college_subject->update();

		$new_record = $this->db->fetch("
			SELECT 
				q.*,
				qa.value
			FROM quest q
			INNER JOIN quest_affiliation_linkage qa_linkage
				ON qa_linkage.quest_id = q.id
			INNER JOIN affiliation a
				ON qa_linkage.affiliation_id = a.id
			INNER JOIN quest_attribute qa
				ON qa.quest_id = q.id
			INNER JOIN quest_attribute_type qat
				ON qat.id = qa.type_id
			WHERE q.id = :id
			", 
			array('id' => $college_subject->id)
		);

		$result = (
			($college_subject->id == $this->record['id']) &&
			($new_record['id'] == $this->record['id']) &&
			($new_record['value'] != $this->record['abbr'])
		);
		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'record - ' . print_r($this->record, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_subject = new CollegeSubjectDAO($this->db, $this->params);
		$college_subject->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_subject->id) && empty($record));
		$error = print_r($college_subject, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
