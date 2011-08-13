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
	const ERROR_EXCEED_MAX_ENROLL = 'Slow down brainiac. We can only add you to six classes at a time';

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
	private $section_dao;

	// enrollment limt
	const ENROLLMENT_LIMIT = 6;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->linkage     = new UserClassLinkageDAO($this->db);
		$this->section_dao = new SectionDAO($this->db);
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
		//error_log('enrollment count - ' . $current_class_count);

		if ($current_class_count >= $this::ENROLLMENT_LIMIT) {
			Logger::Write(self::EVENT_EXCEED_MAX_ENROLL);
			return array(
				'error' => true,
				'message' => self::ERROR_EXCEED_MAX_ENROLL,
			);
		}

			
		$linkage_id = $this->linkage->create(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));

		if ($linkage_id != false) {
			Logger::Write(self::EVENT_NEW_ENROLL);
			$this->section_dao->read(array('id' => $section_id));
			$has_syllabus = $this->section_dao->syllabus_raw != null;
			
			return array(
				'section_id'   => $section_id,
				'has_syllabus' => $has_syllabus,
				'message'      => 'You are now enrolled in ' . $this->section_dao->subject_abbr . '-' . $this->section_dao->course_num . '!',
			);
		} 

		return false;;
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
		return $this->linkage->attribute;
	}

}
