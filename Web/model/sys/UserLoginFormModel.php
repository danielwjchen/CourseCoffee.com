<?php
/**
 * @file
 * Handle user login
 */

class UserLoginFormModel extends FormModel {

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	 /**
	  * Failed to login
		*/
	const ERROR_FAILED_TO_LOGIN = 'The email and password do not match';

	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	/**
	 * Failed to login
	 */
	const EVENT_FAILED_TO_LOGIN = 'Failed login attempt';

	/**
	 * Form fields empty
	 */
	const EVENT_FORM_EMPTY = 'An empty user login submission is made. How is this possible?';

	/**
	 * Password failed
	 */
	const EVENT_FAILED_PASSWORD = 'The password provided does not match the email';

	/**
	 * Form has expired
	 */
	const EVENT_FORM_EXPIRED = 'User login form expired. Please try again.';

	/**
	 * Exceeded max login attempt
	 */
	const EVENT_EXCEEDED_MAX_ATTEMP = 'User exceeded max login attempt';

	/**
	 * New login
	 */
	const EVENT_NEW_LOGIN = 'User Logged in';

	/**
	 * @} End of even_messages
	 */

	/**
	 * New login
	 */
	const REDIRECT = '/home';

	/**
	 * Access to user record
	 */
	private $user_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao = new UserDAO($this->db);
		$this->form_name = 'user_login_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in an hour
		$this->expire = 3600;
	}

	/**
	 * Process the user login request 
	 *
	 * @param string $email
	 *  a string of email address to be user as account
	 * @param string $password
	 *  a string to identifer the user as the owner of the account
	 * @param string $token
	 *  a uniquely generated token to counter CSR attacks
	 *
	 * @return array
	 *  on failure, the method returns an array made of the params plus error 
	 *  message as listed below:
	 *   - email
	 *   - password
	 *   - token
	 *   - error
	 *  on success, the method returnd the newly created user id and a redirect 
	 *  URL as listed below:
	 *   - user_id
	 *   - redirect
	 */
	public function processForm($email, $password, $token) {
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'email' => null,
				'password' => null,
				'token' => $token,
				'error' => self::EVENT_FORM_EXPIRED
			);
		}

		// if there are empty fields... consider it interesting as the 
		// javascript is supposed to catch this.
		if (empty($email) || empty($password)) {
			Logger::write(self::EVENT_FORM_EMPTY, Logger::SEVERITY_HIGH);
			return array(
				'email' => null,
				'password' => null,
				'token' => $token,
				'error' => self::EVENT_FORM_EMPTY
			);
		}

		// look for record
		$encrypted_password = Crypto::Encrypt($password);
		$this->user_dao->read(array('account' => $email));

		// obviously if no such user exists
		if ($this->user_dao->id === null) {
			Logger::write(self::EVENT_FAILED_TO_LOGIN, Logger::SEVERITY_LOW);
			return array(
				'email' => $email,
				'password' => $password,
				'token' => $token,
				'error' => self::ERROR_FAILED_TO_LOGIN,
			);
		// if the password doesn't match... we log this event
		} elseif ($this->user_dao->password !== $encrypted_password) {
			Logger::write(self::EVENT_FAILED_PASSWORD, Logger::SEVERITY_HIGH);
			return array(
				'email' => $email,
				'password' => $password,
				'token' => $token,
				'error' => self::ERROR_FAILED_TO_LOGIN,
			);
		} else {
			Logger::write(self::EVENT_NEW_LOGIN);
			$this->unsetFormToken();
			return array(
				'user_id' => $this->user_dao->id,
				'redirect' => self::REDIRECT,
			);
		}

	}

}
