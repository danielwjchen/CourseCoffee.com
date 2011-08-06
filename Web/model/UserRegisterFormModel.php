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
	 * @defgroup 'dao
	 * @{
	 * Access to database records
	 */
	private $user_dao;
	private $person_dao;
	private $institution_linkage_dao;
	private $institution_dao;
	private $facebook_linkage_dao;
	/**
	 * @} End of "dao"
	 */

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao                = new UserDAO($this->db);
		$this->facebook_linkage_dao    = new UserFacebookLinkageDAO($this->db);

		$this->person_dao              = new PersonDAO($this->db);
		$this->institution_linkage_dao = new UserInstitutionLinkageDAO($this->db);
		$this->institution_dao         = new InstitutionDAO($this->db);

		$this->form_name = 'user_register_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in 10 minutes
		$this->expire = 600;
	}
	
	/**
	 * Process the user registration request 
	 *
	 * @param string $fisrt_name
	 *  user's first name
	 * @param string $last_name
	 *  user's last name
	 * @param string $institution_id
	 *  user's school
	 * @param string $fb_uid
	 *  a facebook user id, this is optional
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
	 *   - email
	 *   - password
	 *   - redirect
	 */
	public function processForm($first_name, $last_name, $institution_id, $fb_uid, $email, $password, $confirm, $token) {
		// if the form is new
		if (empty($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_NEW_ATTEMPT);
			return array(
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'fb_uid'         => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
				'error'          => null,
			);
		} 
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'fb_uid'         => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
				'error'          => self::ERROR_FORM_EXPIRED
			);
		}

		// if there are empty fields... consider it interesting as the 
		// javascript is supposed to catch this.
		if (
			empty($first_name) ||
			empty($last_name) ||
			empty($institution_id) ||
			empty($email) || 
			empty($password)
		) {
			Logger::write(self::EVENT_FORM_EMPTY, Logger::SEVERITY_HIGH);
			return array(
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'fb_uid'         => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
				'error'          => self::ERROR_FORM_EMPTY
			);
		}

		// check if the account is alread taken
		$this->user_dao->read(array('account' => $email));
		if (!empty($this->user_dao->password)) {
			Logger::write(self::EVENT_EMAIL_TAKEN);
			return array(
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'fb_uid'         => $fb_uid,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
				'error'          => self::ERROR_EMAIL_TAKEN,
			);
		}

		// check if the password and confirmation match
		if ($password !== $confirm) {
			return array(
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'fb_uid'         => $fb_uid,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
				'error'          => self::ERROR_PASSWORD_NOT_MATCH,
			);
		}

		// create record
		$encrypted_password = Crypto::Encrypt($password);
		$user_id = $this->user_dao->create(array(
			'account' => $email,
			'password' => $encrypted_password,
		));

		$this->person_dao->create(array(
			'user_id'    => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		));

		$this->institution_linkage_dao->create(array(
			'user_id'        => $user_id,
			'institution_id' => $institution_id,
		));

		// if the user is registering via facebook
		if (!empty($fb_uid)) {
			$this->facebook_linkage_dao->create(array(
				'user_id' => $user_id,
				'fb_uid'  => $fb_uid
			));
		}

		// for some crazy reason, the system failed to create an record and return
		// the primary key
		if (empty($user_id)) {
			Logger::write(self::EVENT_FAILED_TO_CREATE, Logger::SEVERITY_LOW);
			return array(
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'fb_uid'         => $fb_uid,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
				'error'          => self::ERROR_FAILED_TO_CREATE,
			);
		} else {
			Logger::write(self::EVENT_NEW_USER);
			$this->unsetFormToken();
			return array(
				'user_id'  => $user_id,
				'email'    => $email,
				'password' => $password,
				'redirect' => self::REDIRECT,
			);
		}
		
	}

}
