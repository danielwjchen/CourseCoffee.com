<?php
/**
 * @file
 * Oversee linkage among user and classes
 */
class UserEnrollClassModel extends Model {

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAILED_ENROLL     = 'A system error was encountered. Please try again later';
	const ERROR_ALREADY_ENROLLED  = 'You already enrolled in this class';
	const ERROR_EXCEED_MAX_ENROLL = 'You may only add up to six courses';

	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	const EVENT_NEW_ENROLL        = 'User enrolled in class';
	const EVENT_REMOVE_ENROLL     = '';
	const EVENT_ALREADY_ENROLLED  = 'User attempted to double enroll';
	const EVENT_EXCEED_MAX_ENROLL = 'User reached enrollment limit';

	/**
	 * @} End of even_messages
	 */

	// Access to records
	private $linkage;
	private $class_dao;

	// enrollment limt
	const ENROLLMENT_LIMIT = 6;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->linkage   = new UserClassLinkageDAO($this->db);
		$this->class_dao = new CollegeClassDAO($this->db);
	}

	/**
	 * Create linkage between user and section
	 *
	 * @param int $user_id
	 * @param int $section_id
	 *
	 * @return mixed
	 */
	protected function createLinkage($user_id, $section_id) {
		return $this->linkage->create(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));
	}

	/**
	 * Add user to class
	 *
	 * @param int $user_id
	 * @param int $section_id
	 *
	 * @return bool
	 */
	public function AddUserToClass($user_id, $section_id) {
		$is_enrolled = $this->linkage->read(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));

		if ($is_enrolled) {
			Logger::Write(self::EVENT_ALREADY_ENROLLED);
			return array(
				'error' => true,
				'message' => self::ERROR_ALREADY_ENROLLED,
			);
		}

		$current_class_count = $this->linkage->read(array('user_id' => $user_id));

		// debug
		// error_log(__METHOD__ . ' : enrollment count - ' . $current_class_count);

		if ($current_class_count >= $this::ENROLLMENT_LIMIT) {
			Logger::Write(self::EVENT_EXCEED_MAX_ENROLL);
			return array(
				'error' => true,
				'message' => self::ERROR_EXCEED_MAX_ENROLL,
			);
		}

		$linkage_id = $this->createLinkage($user_id, $section_id);

		if ($linkage_id != false) {
			Logger::Write(self::EVENT_NEW_ENROLL);
			$this->class_dao->read(array('id' => $section_id));
			$has_syllabus = $this->class_dao->syllabus_id != null;
			$result = $this->class_dao->attribute;
			$redirect = '/class/' . $result['institution_uri'] . '/' . $result['year'] . '/' . $result['term'] . '/' . $result['subject_abbr'] . '/' . $result['course_num'] . '/' . $result['section_num'];

			// debug
			// error_log(__METHOD__ . ' : section enrolled - ' . print_r($this->class_dao->attribute, true));
			// error_log(__METHOD__ . ' : section redirect - ' . $redirect);
			
			return array(
				'success'      => true,
				'section_id'   => $section_id,
				'redirect'     => $redirect,
				'section_code' => $result['section_code'],
				'has_syllabus' => $has_syllabus,
				'message'      => 'You are now enrolled in ' . $result['section_code'],
			);
		} 

		return array(
			'error'   => true,
			'message' =>''
		);
	}

	/**
	 * Remove user from class
	 *
	 * @param int $user_id
	 * @param int $section_id
	 */
	public function removeUserFromClass($user_id, $section_id) {
		$this->linkage->read(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));
		$this->linkage->destroy();
		Logger::Write(EVENT_REMOVE_ENROLL);
	}

	/**
	 * Get the classes belong to a user
	 *
	 * @param int $user_id
	 */
	public function getUserClass($user_id) {
		$this->linkage->read(array('user_id' => $user_id));
		return $this->linkage->list;
	}

}
