<?php
/**
 * @file
 * Handle user login
 *
 * @see UserSessionModel
 */
class UserLoginFormModel extends FormModel {

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAILED_TO_LOGIN = 'The email and password do not match';
	const ERROR_FORM_EXPIRED    = 'User login form expired. Please try again.';
	const ERROR_FORM_EMPTY      = 'The email and password fields cannot be empty';
	const ERROR_MAX_TRY         = 'Too many failed login attempts';

	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup even_messages
	 * @{
	 * Log messges to track events
	 */
	
	const EVENT_FAILED_TO_LOGIN     = 'Failed login attempt';
	const EVENT_FORM_EMPTY          = 'An empty user login submission is made. How is this possible?';
	const EVENT_FAILED_PASSWORD     = 'The password provided does not match the email';
	const EVENT_FORM_EXPIRED        = 'User login form expired.';
	const EVENT_EXCEEDED_MAX_ATTEMP = 'User exceeded max login attempt';
	const EVENT_NEW_LOGIN           = 'User Logged in';
	const EVENT_MAX_TRY             = 'User exceede max login attempts';

	/**
	 * @} End of even_messages
	 */

	/**
	 * Redirecting after successful login attempt
	 */
	const REDIRECT = '/home';

	/**
	 * @defgroup dao
	 * @{
	 */
	private $user_dao;
	/**
	 * @} End of "dao"
	 */

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao = new UserDAO($this->default_db);

		$this->form_name = 'user_login_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in an hour
		$this->expire = 3600;
	}

	/**
	 * Log in user by facebook uid
	 */
	public function processFBLoginRequest($fb_uid) {
		$fb_linkage_dao = new UserFacebookLinkageDAO($this->default_db);
		$has_record = $fb_linkage_dao->read(array(
			'fb_uid' => $fb_uid
		));

			// debug
			// error_log(__METHOD__ . ' : fb_uid - ' . $fb_linkage_dao->fb_uid);
		if ($has_record) {
			$this->user_dao->read(array('id' => $fb_linkage_dao->user_id));

			// debug
			// error_log(__METHOD__ . ' : user_id - ' . $fb_linkage_dao->user_id);
			
			return array(
				'success'  => true,
				'user_id'  => $fb_linkage_dao->user_id,
				'email'    => $this->user_dao->account,
				'password' => $this->user_dao->password,
				'redirect' => self::REDIRECT,
			);
		} 

		return array('error' => true);
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
		$this->incrementTries();
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'error'    => self::ERROR_FORM_EXPIRED,
				'email'    => null,
				'password' => null,
				'token'    => $token,
			);
		}

		// if user retried to many times..
		if ($this->hasExceededMaxTries()) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_MAX_TRY);
			return array(
				'error'    => self::ERROR_MAX_TRY,
				'email'    => null,
				'password' => null,
				'token'    => $token,
			);
		}

		// if there are empty fields... consider it interesting as the 
		// javascript is supposed to catch this.
		if (empty($email) || empty($password)) {
			Logger::write(self::EVENT_FORM_EMPTY, Logger::SEVERITY_HIGH);
			return array(
				'error'      => self::ERROR_FORM_EMPTY,
				'email'      => null,
				'password'   => null,
				'token'      => $token,
			);
		}

		// look for record
		$encrypted_password = Crypto::Encrypt($password);
		$has_record = $this->user_dao->read(array('account' => $email));

		// obviously if no such user exists
		if (!$has_record) {
			Logger::write(self::EVENT_FAILED_TO_LOGIN, Logger::SEVERITY_LOW);
			return array(
				'error'    => self::ERROR_FAILED_TO_LOGIN,
				'email'    => $email,
				'password' => $password,
				'token'    => $token,
			);

		// if the password doesn't match... we log this event
		} elseif ($this->user_dao->password !== $encrypted_password) {
			Logger::write(self::EVENT_FAILED_PASSWORD, Logger::SEVERITY_HIGH);
			return array(
				'error'    => self::ERROR_FAILED_TO_LOGIN,
				'email'    => $email,
				'password' => $password,
				'token'    => $token,
			);

		// success
		} else {
			Logger::write(self::EVENT_NEW_LOGIN);
			$this->unsetFormToken();
			return array(
				'success'  => true,
				'user_id'  => $this->user_dao->id,
				'redirect' => self::REDIRECT,
			);
		}

	}

}
