<?php
/**
 * @file
 * Represent college section records in database
 *
 * NOTE: this is very different from other DAOs as it only does read but not 
 * create. This is also one of the example that DAO needs to be re-designed.
 */
class CollegeClassSuggestDAO extends ListDAO {

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
		$sql = '
			SELECT 
				s.course_id,
				s.id AS section_id,
				s.num AS section_num,
				c.num AS course_num,
				c.title AS course_title,
				sub.abbr AS subject_abbr,
				sub.title AS subject_title,
				i.name AS institution
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
			WHERE 
		';

		$sql_params   = array();
		$where_clause = array();

		if (isset($params['term_id'])) {
			$where_clause[] = 'it.id = :term_id';
			$sql_params['term_id'] = $params['term_id'];
		}

		if (isset($params['year_id'])) {
			$where_clause[] = 'iy.id = :year_id';
			$sql_params['year_id'] = $params['year_id'];
		}

		if (isset($params['institution_id'])) {
			$where_clause[] = 'i.id = :institution_id';
			$sql_params['institution_id'] = $params['institution_id'];
		}
			
		// a specific course section is given
		if (isset($params['course_id']) && isset($params['section_num'])) {
			$where_clause[] = "s.`course_id` = :course_id";
			$where_clause[] = "s.`num` = :section_num";
			$sql_params['course_id'] = $params['course_id'];
			$sql_params['num'] = $params['num'];

		// get all sections below to a course
		} elseif (isset($params['course_id'])) {
			$where_clause[] = "s.`course_id` = :course_id";
			$sql_params['course_id'] = $params['course_id'];

		// match string pattern
		} elseif (isset($params['like'])) {

			if (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) &&
				isset($params['like']['section_num'])
			) {
				$where_clause[] = 'sub.abbr LIKE :subject_abbr';
				$where_clause[] = 'c.num LIKE :course_num';
				$where_clause[] = 's.num LIKE :section_num';

				$sql_params['subject_abbr'] = $params['like']['subject_abbr'] . '%';
				$sql_params['course_num']   = $params['like']['course_num'] . '%';
				$sql_params['section_num']  = '%' . $params['like']['section_num'] . '%';

			} elseif (
				isset($params['like']['subject_abbr']) &&
				isset($params['like']['course_num']) 
			){

				$where_clause[] = "sub.abbr LIKE :subject_abbr";
				$where_clause[] = "c.num LIKE :course_num";

				$sql_params['subject_abbr'] = $params['like']['subject_abbr'] . '%';
				$sql_params['course_num']   = $params['like']['course_num'] . '%';

			} elseif (isset($params['like']['subject_abbr'])) {
				$where_clause[] = "sub.abbr LIKE :subject_abbr";
				$sql_params['subject_abbr'] = $params['like']['subject_abbr'] . '%';

			}
		} else {
			Logger::Write(__METHOD__ . ' : unknown section pattern - ' . print_r($params, true));

			return false;
		}

		$sql .= implode(' AND ', $where_clause);

		if (isset($params['limit'])) {
			$sql .= "
				LIMIT {$params['limit']['offset']}, {$params['limit']['count']} 
			";
		}


		$this->list = $this->db->fetch($sql, $sql_params);
		return !empty($this->list);


	}
}
