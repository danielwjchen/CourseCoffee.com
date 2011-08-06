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

	const ERROR_FAILED_ENROLL = 'The email and password do not match';

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
		$linkage_id = $this->linkage->create(array(
			'user_id'    => $user_id,
			'section_id' => $section_id,
		));

		if ($linkage_id != false) {
			Logger::Write(EVENT_NEW_ENROLL);
			return array(
				'success' => true,
				'content' => 'You are now enrolled in this class!',
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
