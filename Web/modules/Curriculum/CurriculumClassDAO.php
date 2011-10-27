<?php
/**
 * @file
 * Represent a college class record in database
 */
class CurriculumClassDAO extends DAO implements DAOInterface {

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'subject_id',
			'subject_abbr',
			'course_id',
			'course_num',
			'course_title',
			'course_code',
			'section_id',
			'section_num',
			'section_code',
			'syllabus_status',
			'syllabus_id',
		);
	}

	/**
	 * Implement DAOInterface::create().
	 */
	public function create($params) {
	}

	/**
	 * Implement DAOInterface::read().
	 *
	 * @param array $params
	 *  - id
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function read($params) {
		$sql_param = array();
		$sql = "
			SELECT 
				sec.id AS section_id,
				sec.num AS section_num,
				sec.syllabus_status,
				sec.syllabus_id,
				sec.course_id,
				CONCAT(sub.abbr, '-', crs.num , ' ', sec.num) AS section_code,
				crs.num AS course_num,
				crs.title AS course_title,
				CONCAT(sub.abbr, '-', crs.num) AS course_code,
				sub.id AS subject_id,
				sub.abbr AS subject_abbr,
				sub.title AS subject_title
			FROM `section` sec
			INNER JOIN course crs
				ON sec.course_id = crs.id
			INNER JOIN subject sub
				ON crs.subject_id = sub.id
		";

		if (isset($params['id'])) {
			$sql .= "WHERE sec.id = :id";
			$sql_param = array('id' => $params['id']);
		} else if (
				isset($params['subject_abbr']) &&
				isset($params['course_num']) &&
				isset($params['section_num'])
		) {
			$sql .= "
				WHERE sub.abbr = :subject_abbr
					AND crs.num = :course_num
					AND sec.num = :section_num
			";
			$sql_param = array(
				'subject_abbr'    => $params['subject_abbr'],
				'course_num'      => $params['course_num'],
				'section_num'     => $params['section_num'],
			);

		} else {
			throw new Exception('unknow college class identifier');
		}


		$data = $this->db->fetch($sql, $sql_param);

		// debug
		// error_log('params - ' . print_r($params, true));
		// error_log('data - ' . print_r($data, true));

		return $this->updateAttribute($data);
	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
	}

	/**
	 * Implement DAOInterface::destroy().
	 */
	public function destroy() {
	}

}
