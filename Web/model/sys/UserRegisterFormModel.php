<?php
/**
 * @file
 * Handle user registration
 */

class UserRegisterFormModel extends FormModel {

	/**
	 * @defgroup error_messages 
	 * @{
   * Error messages for the user when an error is encountered
	 */

	/**
	 * Failed to create record in database
	 */
	const ERROR_FAILED_TO_CREATE = 'Oh no! the server monkeys are revolting! Quick! Get the bananas!';

	/**
	 * Form has expired
	 */
	const ERROR_FORM_EXPIRED = 'The form has expire. Please try again.';

	/**
	 * Form fields empty
	 */
	const ERROR_FORM_EMPTY = 'You have empty fileds. Please try again.';

	/**
	 * The email is already taken
	 */
	const ERROR_EMAIL_TAKEN = 'An account is already registered with this emaill address. Please try again.';

	/**
	 * The password and password confirmation don't match
	 */
	const ERROR_PASSWORD_NOT_MATCH = 'The password and confirmation do not match. Please try again.';
	
	/**
	 * @} End of error_messages
	 */

	/**
	 * @defgroup event_messages
	 * @{
	 * Log messges to track events
	 */

	/**
	 * Failed to create record in database
	 */
	const EVENT_FAILED_TO_CREATE = 'Failed to create user';

  /**
	 * New attempt to register 
	 */
	const EVENT_NEW_ATTEMPT = 'New user attempt to register';

	/**
	 * Form fields empty
	 */
	const EVENT_FORM_EMPTY = 'An empty user registration submission is made. How is this possible?';

	/**
	 * New user is created
	 */
	const EVENT_NEW_USER = 'New user registered';

	/**
	 * Form has expired
	 */
	const EVENT_FORM_EXPIRED = 'User registration form expired';

	/**
	 * The email is already taken
	 */
	const EVENT_EMAIL_TAKEN = 'Attempt to register with an existing email account';

	/**
	 * @} End of even_messages
	 */

	/**
	 * Action to take after the form is successfully processed
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
		$this->form_name = 'user_register_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in 10 minutes
		$this->expire = 600;
	}
	
	/**
	 * Process the user registration request 
	 *
	 * @param string $email
	 *  a string of email address to be user as account
	 * @param string $password
	 *  a string to identifer the user as the owner of the account
	 * @param string $confirm
	 *  a string that should be identical to $password
	 * @param string $token
	 *  a uniquely generated token to counter CSR attacks
	 *
	 * @return array
	 *  on failure, the method returns an array made of the params plus error 
	 *  message as listed below:
	 *   - email
	 *   - password
	 *   - confirm
	 *   - token
	 *   - error
	 *  on success, the method returnd the newly created user id and a redirect 
	 *  URL as listed below:
	 *   - user_id
	 *   - redirect
	 */
	public function processForm($email, $password, $confirm, $token) {
		// if the form is new
		if (empty($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_NEW_ATTEMPT);
			return array(
				'email' => null,
				'password' => null,
				'confirm' => null,
				'token' => $token,
				'error' => null,
			);
		// if the form token has expired
		} elseif (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'email' => null,
				'password' => null,
				'confirm' => null,
				'token' => $token,
				'error' => self::ERROR_FORM_EXPIRED
			);
		}

		// if there are empty fields... consider it interesting as the 
		// javascript is supposed to catch this.
		if (empty($email) || empty($password) || empty($confirm)) {
			Logger::write(self::EVENT_FORM_EMPTY, Logger::SEVERITY_HIGH);
			return array(
				'email' => null,
				'password' => null,
				'confirm' => null,
				'token' => $token,
				'error' => self::ERROR_FORM_EMPTY
			);
		}

		// check if the account is alread taken
		$this->user_dao->read(array('account' => $email));
		if (!empty($this->user_dao->password)) {
			Logger::write(self::EVENT_EMAIL_TAKEN);
			return array(
				'email' => $email,
				'password' => $password,
				'confirm' => $confirm,
				'token' => $token,
				'error' => self::ERROR_EMAIL_TAKEN,
			);
		}

		// check if the password and confirmation match
		if ($password !== $confirm) {
			return array(
				'email' => $email,
				'password' => $password,
				'confirm' => $confirm,
				'token' => $token,
				'error' => self::ERROR_PASSWORD_NOT_MATCH,
			);
		}

		// create record
		$encrypted_password = Crypto::Encrypt($password);
		$user_id = $this->user_dao->create(array(
			'account' => $email,
			'password' => $encrypted_password,
		));

		// for some crazy reason, the system failed to create an record and return
		// the primary key
		if (empty($user_id)) {
			Logger::write(self::EVENT_FAILED_TO_CREATE, Logger::SEVERITY_LOW);
			return array(
				'email' => $email,
				'password' => $password,
				'confirm' => $confirm,
				'token' => $token,
				'error' => self::ERROR_FAILED_TO_CREATE,
			);
		} else {
			Logger::write(self::EVENT_NEW_USER);
			$this->unsetFormToken();
			return array(
				'user_id' => $user_id,
				'redirect' => self::REDIRECT,
			);
		}
		
	}

}
