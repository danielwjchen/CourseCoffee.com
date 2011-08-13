<?php
/**
 * @file
 * Over see access to college class records
 */
class CollegeClassModel extends Model {

	/**
	 * def_group class_error
	 * @{
	 * Error message to infor user of the result
	 */
	const NO_CLASS_FOUND = 'We are sorry, but no class could be found with the provided information';

	/**
	 * Access to college class records
	 */
	private $class_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->class_dao = new CollegeClassDAO($this->db);
	}

	/**
	 * A helper method to share code
	 */
	private function returnResult($has_record) {
		if ($has_record) {
			return array(
				'content' => $this->class_dao->attribute,
			);
		}

		return array(
			'error'   => true,
			'message' => self::NO_CLASS_FOUND,
		);
	}

	/**
	 * Get college class information by section id
	 *
	 * @param string $section_id
	 */
	public function getClassById($section_id) {
		$has_record = $this->class_dao->read(array('id' => $section_id));
		return $this->returnResult($has_record);
	}

	/**
	 * Get college class information by name
	 *
	 * @param string institution_uri
	 *   an URI safe version of the college name
	 * @param string year
	 * @param string term
	 * @param string subject_abbr
	 * @param string course_num
	 * @param string section_num
	 */
	public function getClassByURI($institution_uri, $year, $term, $subject_abbr, $course_num, $section_num) {
		$has_record = $this->class_dao->read(array(
			'institution_uri'  => $institution_uri,
			'year'             => $year,
			'term'             => $term,
			'subject_abbr'     => strtoupper($subject_abbr),
			'course_num'       => strtoupper($course_num),
			'section_num'      => strtoupper($section_num),
		));
		return $this->returnResult($has_record);
	}

}
