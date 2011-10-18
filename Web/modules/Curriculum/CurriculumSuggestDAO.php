<?php
/**
 * @file
 * Represent college section records in database
 */
class CurriculumSuggestDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement DAOInterface::read().
	 *
	 * @param array $params
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
				sub.title AS subject_title
			FROM `section` s
			INNER JOIN course c
				ON s.course_id = c.id
			INNER JOIN subject sub
				ON c.subject_id = sub.id
			WHERE 
		';

		$sql_params   = array();
		$where_clause = array();

			
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
		$sql .= ' ORDER BY sub.abbr, c.num, s.num';


		$this->list = $this->db->fetchList($sql, $sql_params);
		return !empty($this->list);


	}
}
