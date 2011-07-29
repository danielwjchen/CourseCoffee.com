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
	const ERROR_FAILED_TO_CREATE   = 'Oh no! the server monkeys are revolting! Quick! Get the bananas!';
	const ERROR_FORM_EXPIRED       = 'The form has expire. Please try again.';
	const ERROR_FORM_EMPTY         = 'You have empty fileds. Please try again.';
	const ERROR_EMAIL_TAKEN        = 'An account is already registered with this emaill address. Please try again.';
	const ERROR_PASSWORD_NOT_MATCH = 'The password and confirmation do not match. Please try again.';
	
	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup event_messages
	 * @{
	 * Log messges to track events
	 */
	const EVENT_FAILED_TO_CREATE = 'Failed to create user';
	const EVENT_NEW_ATTEMPT      = 'New user attempt to register';
	const EVENT_FORM_EMPTY       = 'An empty user registration submission is made. How is this possible?';
	const EVENT_NEW_USER         = 'New user registered';
	const EVENT_FORM_EXPIRED     = 'User registration form expired';
	const EVENT_EMAIL_TAKEN      = 'Attempt to register with an existing email account';
	const EVENT_UNKNOWN_SCHOOL   = 'Attempt to register with a unknown school. Record created';
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
		$this->user_profile_dao = new UserProfileDAO($this->db);
	}

	/**
	 * Get user's profile
	 */
	public function getProfile($user_id) {
		$this->user_profile_dao->read(array('id' => $user_id));
		return $this->user_profile_dao->attribute;
	}

	/**
	 * Process requests to update UserProfile
	 */
	public function processForm() {
	}
}
