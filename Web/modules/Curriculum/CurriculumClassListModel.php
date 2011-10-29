<?php
/**
 * @file
 * Manage access to class list
 *
 * This is not secured by token
 */
class CurriculumClassListModel extends Model {

	/**
	 * Number of records to fetch
	 */
	const COUNT = 10;

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */
	const ERROR_FAILED_TO_CREATE = 'All servers down!!11!one!!';
	const ERROR_FORM_EMPTY       = 'The email and password fields cannot be empty';
	const ERROR_FORM_EXPIRED     = 'The form has expired. Please try again.';
	const ERROR_NO_ENROLLMENT    = 'hmmm... it seems you are not enrolled in classes.';
	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	const EVENT_FAILED_TO_CREATE = 'Failed create task';
	const EVENT_FORM_EMPTY       = 'An empty task form is submitted. How is this possible?';
	const EVENT_FORM_EXPIRED     = 'Task creation form expired.';
	/**
	 * @} End of even_messages
	 */


	/**
	 * Access to class record
	 */
	private $list_dao;


	/**
	 * Suggest a list of class based on input
	 *
	 * @param int $institution_id
	 * @param int $year_id
	 * @param int $term_id
	 * @param string $string
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - message:
	 *   - list:
	 *  On failure:
	 *   - error:
	 *   - message:
	 */
	public function suggestClassList($institution_id, $year_id, $term_id, $string) {

		$string = preg_replace('/[^a-zA-Z0-9&\s\-]/i', '', $string);
		preg_match('/^[a-z]{1,12}/i', $string, $matches);
		$subject_abbr = is_array($matches) ? reset($matches) : '';

		$course_string = str_replace($subject_abbr, '', $string);
		preg_match('/[0-9]{1,3}[a-z]{0,1}/i', $course_string, $matches);
		$course_num   = is_array($matches) ? trim(reset($matches)) : '';

		$section_string = str_replace($course_num, '', $course_string);
		$section_num = null;
		if ($section_string != '') {
			preg_match('/[0-9]{1,3}[a-z]{0,1}$/i', $section_string, $section_matches);
			$section_num = is_array($section_matches) ? reset($section_matches) : '';
		}

		$params = array(
			'institution_id' => $institution_id,
			'year_id'        => $year_id,
			'term_id'        => $term_id,
		);

		/**
		 * debug stuff
		 *
		error_log('subject = ' . $string . ' - ' . $subject_abbr);
		error_log('course = ' . $course_string . ' - ' . $course_num);
		error_log('section = ' . $section_string . '-' . $section_num);
		 */

		if (!empty($subject_abbr)) {
			$params['like']['subject_abbr'] = $subject_abbr;
		}
		
		if (!empty($course_num)) {
			$params['like']['course_num'] = $course_num;
		}

		if (!empty($section_num)) {
			$params['like']['section_num'] = $section_num;
		}

		$params['limit']['offset'] = 0;
		$params['limit']['count']  = 10;

		$this->list_dao = new CurriculumClassListDAO($this->institution_db);
		$has_records = $this->list_dao->read($params);

		if ($has_records) {
			return array(
				'success' => true,
				'message' => '',
				'list'    => $this->list_dao->list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
	}

	/**
	 * Fetch a list of enrolled class for user
	 *
	 * @param int $user_id
	 * @param int $institution_id
	 * @param int $year_id
	 * @param int $term_id
	 *
	 * @return array
	 */
	public function fetchUserClassList($user_id, $institution_id, $year_id, $term_id) {
		$this->list_dao = new UserClassListDAO($this->institution_db);

		$has_record = $this->list_dao->read(array(
			'user_id'        => $user_id,
			'institution_id' => $institution_id,
			'year_id'        => $year_id,
			'term_id'        => $term_id,
		));

		if (!$has_record) {
			return array('error' => self::ERROR_NO_ENROLLMENT);
		}
		
		$result = array();
		if (isset($this->list_dao->list['section_id'])) {
			$result[$this->list_dao->list['section_id']] = $this->list_dao->list['section_code'];

		} else {
			foreach ($this->list_dao->list as $key => $value) {
				$result[$value['section_id']] = $value['section_code'];
			}
		}

		// debug output
		// error_log(__METHOD__ . ' : user class list - ' . print_r($this->list_dao->list, true));
		// error_log(__METHOD__ . 'formatted user class list - ' . print_r($result, true));

		return $result;
	}

	/**
	 * Get a list of class based on syllabus status
	 */
	public function getClassListBySyllabusStatus($syllabus_status, $timestamp, $paginate = 0) {
		$this->list_dao = new CurriculumClassListDAO($this->institution_db);
		$has_records = $this->list_dao->read(array(
			'syllabus_status' => $syllabus_status,
			'limit' => array(
				'offset' => $paginate * self::COUNT,
				'count'  => self::COUNT,
			)
		));

		if ($has_records) {
			return array(
				'success' => true,
				'message' => '',
				'list'    => $this->list_dao->list,
			);
		} else {
			return array(
				'error'   => true,
				'message' => '',
			);
		}
	}

}
