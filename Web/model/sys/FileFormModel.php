<?php
/**
 * @file
 * Handle file upload requests
 */
class FileFormModel extends FormModel{

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	 /**
	  * Failed to login
		*/
	const ERROR_FAILED_TO_LOGIN = 'The email and password do not match';
	const ERROR_FORM_EXPIRED    = 'User login form expired. Please try again.';
	const ERROR_FORM_EMPTY      = 'The email and password fields cannot be empty';
	const ERROR_MAX_TRY         = 'Too many failed login attempts';

	/**
	 * @} End of error_messages
	 */

	/**
	 * Access to user file record
	 */
	private $user_file_dao;


	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao = new UserDAO($this->sys_db);
		$this->user_cookie_dao = new UserCookieDAO($this->sys_db);
		$this->form_name = 'user_login_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in an hour
		$this->expire = 3600;
	}

	/**
	 * Process the file upload request
	 */
	public function processForm($user_id, $token) {
		$this->incrementTries();
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'token'    => $token,
				'error'    => self::ERROR_FORM_EXPIRED
			);
		}

	}

}
