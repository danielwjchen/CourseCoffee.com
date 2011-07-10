<?php

require_once __DIR__ . '/../bootstrap.php';

class CollegeCourseDAOTestCase extends DAOTestCase implements DAOTestCaseInterface{

	/**
	 * Implement DAOTestCaseInterface::testInstantiation()
	 */
	function testInstantiation() {
		$college_course = new CollegeCourseDAO($this->db, $this->params);
		$result = ($college_course->id == $this->record['id']);
		$error = 
			'record - ' . print_r($this->record, true) . "\n" . 
			'object - ' . print_r($college_course->attribute, true) . "\n"
		;

		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testCreate()
	 */
	function testCreate() {
		$params = array(
			'title' => 'Introduction to C++',
			'description' => 'Most awesome course to study in college',
			'college' => $this->params['college'],
			'subject' => $this->params['subject'],
			'num' => '132',
		);

		$college_course = new CollegeCourseDAO($this->db);
		$college_course->create($params);

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
			WHERE q.objective = :title
				AND a.name = :college
				AND qat.name = :type
			", 
			array(
				'title' => $params['title'],
				'college' => $params['college'],
				'type' => 'college_course_num',
			)
		);

		$result = (
			$new_record['objective'] == $params['title'] &&
			$new_record['value']  == $params['num']
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
		$college_course = new CollegeCourseDAO($this->db);
		$college_course->read($this->params);
		$result = (
			$college_course->id == $this->record['id'] &&
			$college_course->num == $this->record['num'] &&
			$college_course->title == $this->record['title']
		);
		$error = 
			'record - ' . print_r($this->params, true) . "\n" . 
			'object - ' . print_r($college_course->attribute, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testUpdate().
	 */
	function testUpdate() {
		$college_course = new CollegeCourseDAO($this->db);
		$college_course->read($this->params);
		$college_course->title = 'Macro Economy';
		$college_course->update();

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
			array('id' => $college_course->id)
		);

		$result = (
			($college_course->id == $this->record['id']) &&
			($new_record['id'] == $this->record['id']) &&
			($new_record['objective'] != $this->record['title'])
		);
		$error = 
			'new record - ' . print_r($new_record, true) . "\n" . 
			'record - ' . print_r($this->record, true) . "\n" . 
			'object - ' . print_r($college_course->attribute, true) . "\n"
		;
		$this->assertTrue($result, $error);
	}

	/**
	 * Implement DAOTestCaseInterface::testDestroy().
	 */
	function testDestroy() {
		$college_course = new CollegeCourseDAO($this->db, $this->params);
		$college_course->destroy();
		$record = $this->db->fetch(
			'SELECT * FROM quest WHERE id = :id', 
			array('id' => $this->record['id'])
		);
		$result = (empty($college_course->id) && empty($record));
		$error = print_r($college_course, true) . print_r($record, true);

		$this->assertTrue($result, $error);
	}

}
