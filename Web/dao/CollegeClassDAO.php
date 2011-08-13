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
			'institution_uri',
			'subject_id',
			'subject_abbr',
			'course_id',
			'course_num',
			'course_title',
			'course_description',
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
		$sql_param = array();
		$sql = "
			SELECT 
				sec.id AS section_id,
				sec.num AS section_num,
				sec.syllabus_status,
				sec.course_id,
				crs.num AS course_num,
				crs.title AS course_title,
				crs.description AS course_description,
				sub.id AS subject_id,
				sub.abbr AS subject_abbr,
				sub.title AS subject_title,
				it.id AS term_id,
				it.name AS term,
				iy.id AS year_id,
				iy.period AS year,
				i.id AS institution_id,
				i.uri AS institution_uri,
				i.name AS institution
			FROM `section` sec
			INNER JOIN course crs
				ON sec.course_id = crs.id
			INNER JOIN subject sub
				ON crs.subject_id = sub.id
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
			$sql .= "WHERE sec.id = :id";
			$sql_param = array('id' => $params['id']);
		} else if (
				isset($params['institution_uri']) &&
				isset($params['year']) &&
				isset($params['term']) &&
				isset($params['subject_abbr']) &&
				isset($params['course_num']) &&
				isset($params['section_num'])
		) {
			$sql .= "
				WHERE i.uri = :institution_uri
					AND iy.period = :year
					AND it.name = :term
					AND sub.abbr = :subject_abbr
					AND crs.num = :course_num
					AND sec.num = :section_num
			";
			$sql_param = array(
				'institution_uri' => $params['institution_uri'],
				'year'            => $params['year'],
				'term'            => $params['term'],
				'subject_abbr'    => $params['subject_abbr'],
				'course_num'      => $params['course_num'],
				'section_num'     => $params['section_num'],
			);
		} else if (
				isset($params['institution_id']) &&
				isset($params['year_id']) &&
				isset($params['term_id']) &&
				isset($params['subject_abbr']) &&
				isset($params['course_num']) &&
				isset($params['section_num'])
		) {
			$sql .= "
				WHERE i.id = :institution_id
					AND iy.id = :year_id
					AND it.id = :term_id
					AND sub.abbr = :subject_abbr
					AND crs.num = :course_num
					AND sec.num = :section_num
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


		$data = $this->db->fetch($sql, $sql_param);

		// debug
		// error_log('params - ' . print_r($params, true));
		// error_log('data - ' . print_r($data, true));

		return $this->updateAttribute($data);
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
