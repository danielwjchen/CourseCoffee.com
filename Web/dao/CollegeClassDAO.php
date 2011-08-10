<?php
/**
 * @file
 * Represent a college class record in database
 */
class CollegeClassDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'institution_id',
			'institution',
			'subject_id',
			'subject_abbr',
			'course_id',
			'course_num',
			'course_title',
			'section_id',
			'section_num',
			'syllabus_status',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
	}

	/**
	 * Extend DAO::read().
	 *
	 * @param array $params
	 *  - id
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 *  - subject_abbr
	 *  - course_num
	 *  - section_num
	 */
	public function read($params) {
		$sql = "
			SELECT 
				s.id AS section_id,
				s.num AS section_num,
				s.syllabus_status,
				s.course_id,
				c.num AS course_num,
				c.title AS course_title,
				c.description AS course_description,
				sub.abbr AS subject_abbr,
				sub.title AS subject_title
			FROM `section` s
			INNER JOIN course c
				ON s.course_id = c.id
			INNER JOIN subject sub
				ON c.subject_id = sub.id
			INNER JOIN subject_term_linkage st_linkage
				ON sub.id = st_linkage.subject_id
			INNER JOIN institution_term it
				ON st_linkage.term_id = it.id
			INNER JOIN institution_year iy
				ON it.year_id = iy.id
			INNER JOIN institution_year_linkage iy_linkage
				ON iy.id = iy_linkage.year_id
			INNER JOIN institution i
				ON iy_linkage.institution_id = i.id
		";

		if (isset($params['id'])) {
			$sql = " WHERE s.id = :id";
			$sql_param = array('id' => $params['id']);
		} else if (
				isset($params['institution_id']) &&
				isset($params['year_id']) &&
				isset($params['term_id']) &&
				isset($params['subject_abbr']) &&
				isset($params['course_num']) &&
				isset($params['section_num'])
		) {
			$sql = "
				WHERE i.id = :institution_id
					AND iy.id = :year_id
					AND it.id = :term_id
					AND sub.abbr LIKE :subject_abbr
					AND c.num LIKE :course_num
			";
			$sql_param = array(
				'institution_id' => $params['institution_id'],
				'year_id'        => $params['year_id'],
				'term_id'        => $params['term_id'],
				'subject_abbr'   => $params['subject_abbr'],
				'course_num'     => $params['course_num'],
				'section_num'    => $params['section_num'],
			);
		} else {
			throw new Exception('unknow college class identifier');
		}

		$this->list = $this->db->fetch($sql, $sql_param);
		return !empty($this->list);
	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
	}

}
