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

	const ERROR_ALREADY_HAS_SYLLABUS = "hmmm... someone already uploaded a syllabus to this class, but that's okay!";
	const EVENT_ALREADY_HAS_SYLLABUS = 'Attempt to upload syllabus to a class already has one';
	const SYLLABUS_SUCCESS = 'Congratulation! The syllabus is now uploaded!';

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
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - meessage:
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
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - meessage:
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
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - meessage:
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

	/**
	 * Check if class already has syllabus uploaded
	 *
	 * @param int $section_id
	 *
	 * @return bool
	 *  return false where there is no syllabus
	 */
	public function hasClassSyllabus($section_id) {
		$section_dao = new SectionDAO($this->db);
		$has_record = $section_dao->read(array('id' => $section_id));
		return empty($section_dao->syllabus_id);
	}

}
