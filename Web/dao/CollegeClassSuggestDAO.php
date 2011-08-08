<?php
/**
 * @file
 * Represent college section records in database
 *
 * NOTE: this is very different from other DAOs as it only does read but not 
 * create. This is also one of the example that DAO needs to be re-designed.
 */
class CollegeClassSuggestDAO extends DAO implements DAOInterface{

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'institution_id',
			'institution',
			'subject_id',
			'subject_title',
			'subject_abbr',
			'course_id',
			'course_title',
			'course_num',
			'section_id',
			'section_num',
		);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create().
	 */
	public function create($params) {
		if (!isset($params['course_id']) || !isset($params['num'])) {
			throw new Exception('incomplete college section pramas - ' . print_r($params, true));
			return ;

		}else{
			return $this->db->insert("
				INSERT INTO `section` (`course_id`, `num`)
				VALUES (:course_id, :num)",
			array(
				'course_id' => $params['course_id'], 
				'num' => $params['num']
			));

		}

	}

	/**
	 * Extend DAO::read().
	 *
	 * @param array $params
	 *  - id
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 *  - course_id
	 *  - section_num
	 *  - like
	 *     - institution_id
	 *     - year_id
	 *     - term_id
	 *     - subject_abbr
	 *     - course_num
	 *     - section_num
	 */
	public function read($params) {
		$sql = '';
		$sql_params = array();
		
		if (isset($params['id'])) {
			$sql = "
				SELECT 
					s.course_id,
					s.id AS section_id,
					c.num AS course_num,
					c.title AS course_title,
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
				WHERE s.`id` = :id
			";
			$data = $this->db->fetch($sql, array('id' => $params['id']));
			return $this->updateAttribute($data);
		} 

		if (
			!isset($params['term_id']) ||
			!isset($params['year_id']) ||
			!isset($params['institution_id']) 
		) {
			throw new Exception('unknown section identifier');
			return false;
		}

		$sql_params = array(
			'term_id'        => $params['term_id'],
			'year_id'        => $params['year_id'],
			'institution_id' => $params['institution_id'],
		);
		
		// a specific course section is given
		if (isset($params['course_id']) && isset($params['section_num'])) {
			$sql = "
				SELECT 
					s.course_id,
					s.id AS section_id,
					c.num AS course_num,
					c.title AS course_title,
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
				WHERE i.id = :institution_id
					AND iy.id = :year_id
					AND it.id = :term_id
					AND s.`course_id` = :course_id 
					AND s.`num` = :section_num
			";
			$sql_params = array_merge($sql_params, array(
				'course_id' => $params['course_id'],
				'num' => $params['num'],
			));

		// get all sections below to a course
		} elseif (isset($params['course_id'])) {
			$sql = "
				SELECT 
					s.course_id,
					s.id AS section_id,
					c.num AS course_num,
					c.title AS course_title,
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
				WHERE i.id = :institution_id
					AND iy.id = :year_id
					AND it.id = :term_id
					AND s.`course_id` = :course_id
			";
			$sql_params = array('course_id' => $params['course_id']);
			$this->list = $this->db->fetch($sql, $sql_params);
			return empty($data);

		// match string pattern
		} elseif (isset($params['like'])) {

			if (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) &&
				isset($params['like']['section_num'])
			) {
				$sql = "
					SELECT 
						s.course_id,
						s.id AS section_id,
						s.num AS section_num,
						c.num AS course_num,
						c.title AS course_title,
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
					WHERE i.id = :institution_id
						AND iy.id = :year_id
						AND it.id = :term_id
						AND sub.abbr LIKE :subject_abbr
						AND c.num LIKE :course_num
						AND s.num LIKE :section_num
				";

				$sql_params = array_merge($sql_params, array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%',
					'course_num'   => '%' . $params['like']['course_num'] . '%',
					'section_num'  => '%' . $params['like']['section_num'] . '%',
				));

			} elseif (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) 
			){

				// we have a course number that hopefully is legit enough to suggest 
				// sections
				if (strlen($params['like']['course_num']) >= 2) {
					$sql = "
						SELECT 
							s.course_id,
							s.id AS section_id,
							s.num AS section_num,
							c.num AS course_num,
							c.title AS course_title,
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
						WHERE i.id = :institution_id
							AND iy.id = :year_id
							AND it.id = :term_id
							AND sub.abbr LIKE :subject_abbr
							AND c.num LIKE :course_num
					";
				} else {
					$sql = "
						SELECT 
							c.id AS course_id,
							c.num AS course_num,
							c.title AS course_title,
							sub.abbr AS subject_abbr,
							sub.title AS subject_title
						FROM `course` c
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
						WHERE i.id = :institution_id
							AND iy.id = :year_id
							AND it.id = :term_id
							AND sub.abbr LIKE :subject_abbr
							AND c.num LIKE :course_num
					";
				}

				$sql_params = array_merge($sql_params, array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%',
					'course_num'   => '%' . $params['like']['course_num'] . '%',
				));

			} elseif (isset($params['like']['subject_abbr'])) {
				$sql = "
					SELECT 
						c.id AS course_id,
						c.num AS course_num,
						c.title AS course_title,
						sub.abbr AS subject_abbr,
						sub.title AS subject_title
					FROM `course` c
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
					WHERE i.id = :institution_id
						AND iy.id = :year_id
						AND it.id = :term_id
						AND sub.abbr LIKE :subject_abbr
				";
				$sql_params = array_merge($sql_params, array(
					'subject_abbr' => '%' . $params['like']['subject_abbr'] . '%'
				));

			} else {
				throw new Exception('unknown section pattern');
			}

			if (isset($params['limit'])) {
				$sql .= "
					LIMIT {$params['limit']['offset']}, {$params['limit']['count']} 
				";
			}

			$this->list = $this->db->fetch($sql, $sql_params);
			return empty($this->list);


		} else {
			throw new Exception('unknown section identifier');

		}

		return false;

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `section` SET
				`course_id` = :course_id,
				`num` = :num
			WHERE `id` = :id
		";
		$this->db->perform($sql, array(
			'course_id' => $this->attr['course_id'], 
			'num' => $this->attr['num']
		));

	}

	/**
	 * Extend DAO::destroy().
	 */
	public function destroy() {
		$sql = "DELETE FROM `section` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}
}
