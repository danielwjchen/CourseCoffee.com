<?php
/**
 * @file
 * Manage user's profile
 */
class UserProfileFormModel extends FormModel {

	/**
	 * @defgroup error_messages 
	 * @{
   * Error messages for the user when an error is encountered
	 */
	const ERROR_FAIL_TO_LOAD   = 'Oh no! the server monkeys are revolting! Quick! Get the bananas!';
	
	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup event_messages
	 * @{
	 * Log messges to track events
	 */
	const EVENT_FAIL_TO_LOAD = 'Failed to load user profile';
	/**
	 * @} End of event_messages
	 */

	/**
	 * Action to take after the form is successfully processed
	 */
	const REDIRECT = '/home';

	/**
	 * Access to user record
	 */
	private $user_profile_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_profile_dao = new UserProfileDAO();
	}

	/**
	 * Get user's profile info
	 */
	public function getProfile($user_id) {
		$user_profile = Session::Get('user_profile');
		if (!empty($user_profile)) {
			return $user_profile;
		}

		$has_record = $this->user_profile_dao->read(array('id' => $user_id));

		if ($has_record ) {
			$result = $this->user_profile_dao->attribute;;
			Session::Set('user_profile', $result);
			$result['success'] = true;
			return $result;
		} else {
			return array(
				'error'   => true,
				'message' => self::ERROR_FAIL_TO_LOAD,
			);
		}
	}

	/**
	 * Process requests to update UserProfile
	 */
	public function processForm() {
	}
}
