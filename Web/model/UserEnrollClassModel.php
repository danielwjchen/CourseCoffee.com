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

	const ERROR_FAILED_ENROLL    = 'A system error was encountered. Please try again later';
	const ERROR_ALREADY_ENROLLED = 'You already enrolled in this class';

	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	const EVENT_NEW_ENROLL    = '';
	const EVENT_REMOVE_ENROLL = '';
	const EVENT_ALREADY_ENROLLED = 'User attempted to double enroll';

	/**
	 * @} End of even_messages
	 */

	// access to records
	private $linkage;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->linkage = new UserClassLinkageDAO($this->db);
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
			
		$linkage_id = $this->linkage->create(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));

		if ($linkage_id != false) {
			Logger::Write(EVENT_NEW_ENROLL);
			return array(
				'section_id' => $section_id,
				'message'    => 'You are now enrolled in this class!',
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
