<?php
/**
 * @file
 * Oversee linkage among user and classes
 *
 * @to-do
 *  This class should be protected by token!
 */
class UserEnrollClassModel extends Model {

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAILED_ENROLL     = 'A system error was encountered. Please try again later';
	const ERROR_SYSTEM_FAIL       = 'Holy giraffs!!1! The server monkeys are going bananas!';
	const ERROR_ALREADY_ENROLLED  = 'You already enrolled in this class';
	const ERROR_EXCEED_MAX_ENROLL = 'You may only add up to six classes';

	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	const EVENT_NEW_ENROLL        = 'User enrolled in class';
	const EVENT_REMOVE_ENROLL     = 'User removed class';
	const EVENT_SYSTEM_FAIL       = 'System fails to create linkage among user and class';
	const EVENT_ALREADY_ENROLLED  = 'User attempted to double enroll';
	const EVENT_EXCEED_MAX_ENROLL = 'User reached enrollment limit';

	/**
	 * @} End of even_messages
	 */

	// enrollment limt
	const ENROLLMENT_LIMIT = 6;

	/**
	 * Implement Model::defineDAO()
	 */
	protected function defineDAO() {
		return array(
			'linkage' => array(
				'class' => 'UserSectionLinkageDAO',
				'db'    => self::INSTITUTION_DB,
			),
			'curriculum_class' => array(
				'class' => 'CurriculumClassDAO',
				'db'    => self::INSTITUTION_DB,
			),
		);
	}

	/**
	 * Create linkage between user and section
	 *
	 * @param int $user_id
	 * @param int $section_id
	 *
	 * @return mixed
	 */
	public function createLinkage($user_id, $section_id) {
		return $this->dao['linkage']->create(array(
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
		$is_enrolled = $this->dao['linkage']->read(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));

		$this->dao['curriculum_class']->read(array('id' => $section_id));
		$has_syllabus = $this->dao['curriculum_class']->syllabus_id != 0;
		$result = $this->dao['curriculum_class']->attribute;
		$redirect = '/class/' . $result['subject_abbr'] . '/' . $result['course_num'] . '/' . $result['section_num'];

		if ($is_enrolled) {
			Logger::Write(self::EVENT_ALREADY_ENROLLED);
			return array(
				'error'    => 'already_enrolled',
				'redirect' => $redirect,
				'message'  => self::ERROR_ALREADY_ENROLLED,
			);
		}

		$current_class_count = $this->dao['linkage']->read(array('user_id' => $user_id));

		// debug
		// error_log(__METHOD__ . ' : enrollment count - ' . $current_class_count);

		if ($current_class_count >= $this::ENROLLMENT_LIMIT) {
			Logger::Write(self::EVENT_EXCEED_MAX_ENROLL);
			return array(
				'error'    => 'exceed_max',
				'redirect' => $redirect,
				'message'  => self::ERROR_EXCEED_MAX_ENROLL,
			);
		}

		$linkage_id = $this->createLinkage($user_id, $section_id);

		if ($linkage_id != false) {
			Logger::Write(self::EVENT_NEW_ENROLL);

			// debug
			// error_log(__METHOD__ . ' : section enrolled - ' . print_r($this->dao['curriculum_class']->attribute, true));
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

		// this only happens when system fails to create for mysterious reason.
		Logger::Write(self::EVENT_SYSTEM_FAIL);
		return array(
			'error'   => true,
			'message' => ERROR_SYSTEM_FAIL,
		);
	}

	/**
	 * Remove user from class
	 *
	 * This will need to be improved and check if the user is actually enrolled 
	 * in the requested class, and check if the user is actually removed.
	 *
	 * @param int $user_id
	 * @param int $section_id
	 */
	public function removeUserFromClass($user_id, $section_id) {
		$this->dao['linkage']->read(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));
		$this->dao['linkage']->destroy();
		Logger::Write(EVENT_REMOVE_ENROLL);
		return array(
			'success' => true,
		);
	}

	/**
	 * Get the classes belong to a user
	 *
	 * @param int $user_id
	 */
	public function getUserSection($user_id) {
		$this->dao['linkage']->read(array('user_id' => $user_id));
		return $this->dao['linkage']->list;
	}

}
