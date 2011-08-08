<?php
/**
 * @file
 * Manage access to class list
 *
 * This is not secured by token
 */
class CollegeClassListModel extends Model {

	/**
	 * Access to class record
	 */
	private $class_list;


	/**
	 * Suggest a list of class based on input
	 *
	 * @param int $institution_id
	 * @param int $year_id
	 * @param int $term_id
	 * @param string $string
	 *
	 * @return array
	 */
	public function suggestClassList($institution_id, $year_id, $term_id, $string) {

		$string = preg_replace('/[^a-zA-Z0-9]/i', '', $string);
		preg_match('/^[a-z]{1,4}/i', $string, $matches);
		$subject_abbr = $matches[0];

		$course_string = str_replace($subject_abbr, '', $string);
		preg_match('/[0-9]{1,3}[a-z]{0,1}/i', $course_string, $matches);
		$course_num   = trim($matches[0]);

		$section_string = str_replace($course_num, '', $course_string);
		$section_num = null;
		if ($section_string != '') {
			preg_match('/[0-9]{1,3}[a-z]{0,1}$/i', $section_string, $section_matches);
			$section_num = $section_matches[0];
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
			error_log('sadfsdf');
		}

		$params['limit']['offset'] = 0;
		$params['limit']['count']  = 10;

		$this->class_list = new CollegeClassSuggestDAO($this->db);
		$this->class_list->read($params);
		return $this->class_list->list;
	}

	/**
	 * Fetch a list of enrolled class for user
	 *
	 * @param string $user_id
	 *
	 * @return array
	 */
	public function fetchUserClassList($user_id) {
		$this->class_list = new UserClassListDAO($this->db);
		$this->class_list->read(array('user_id' => $user_id));

		return array(
			'content' => $this->class_list->list,
		);
	}

}
