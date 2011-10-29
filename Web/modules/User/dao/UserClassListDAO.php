<?php
/**
 * @file
 * Represent a list of classes belong to user
 */
class UserClassListDAO extends ListDAO implements ListDAOInterface {

	/**
	 * Implement ListDAOInterface::read().
	 *
	 * @param array $params
	 *  - user_id
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 */
	public function read(array $params = null) {
		if (!isset($params['user_id']) ||
				!isset($params['institution_id']) ||
				!isset($params['year_id']) ||
				!isset($params['term_id'])
		) {
			throw new Exception('unknow user class list identifier - ' . print_r($params, true));
			return false;
		}

		$sql = "
			SELECT 
				sec.id AS section_id,
				CONCAT(sub.abbr, '-', crs.num, ' ', sec.num) AS section_code
			FROM `section` sec
			INNER JOIN user_section_linkage us_linkage
				ON sec.id = us_linkage.section_id
			INNER JOIN course crs
				ON sec.course_id = crs.id
			INNER JOIN subject sub
				ON crs.subject_id = sub.id
			WHERE us_linkage.user_id = :user_id
		";

		$sql_param = array('user_id' => $params['user_id']);

		$this->list = $this->db->fetchList($sql, $sql_param);

		// debug
		// error_log(__METHOD__ . ' : user class list param - ' . print_r($params, true));
		// error_log(__METHOD__ . ' : user class list - ' . print_r($this->list, true));

		return !empty($this->list);
	}
}
