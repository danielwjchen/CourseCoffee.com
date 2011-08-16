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
	const ERROR_FORM_EXPIRED       = 'The form has expired. Please try again.';
	const ERROR_FORM_EMPTY         = 'You have empty fields. Please try again.';
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
	private $user_setting_dao;
	private $facebook_linkage_dao;
	private $person_dao;
	private $institution_linkage_dao;
	private $institution_dao;
	private $institution_year_dao;
	private $institution_term_dao;
	/**
	 * @} End of "dao"
	 */

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao                = new UserDAO($this->db);
		$this->user_setting_dao        = new UserSettingDAO($this->db);
		$this->facebook_linkage_dao    = new UserFacebookLinkageDAO($this->db);

		$this->person_dao              = new PersonDAO($this->db);
		$this->institution_linkage_dao = new UserInstitutionLinkageDAO($this->db);
		$this->institution_dao         = new InstitutionDAO($this->db);
		$this->institution_year_dao    = new InstitutionYearDAO($this->db);
		$this->institution_term_dao    = new InstitutionTermDAO($this->db);

		$this->form_name = 'user_register_form';
		// form submission is limite to 5 times
		$this->max_try = 5;
		// form expires in 30 minutes
		$this->expire = 1800;
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
	 * @param string $year
	 * @param string $term
	 *  a facebook user id, this is optional
	 * @param string $email
	 *  a string of email address to be user as account
	 * @param string $password
	 *  a string to identifer the user as the owner of the account
	 * @param string $confirm
	 *  a string that should be identical to $password
	 * @param string $token
	 *  a uniquely generated token to counter CSR attacks
	 * @param int $fb_uid
	 *  user's facebook uid, only exists when the user is registered through 
	 *  facebook
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
	public function processForm($first_name, $last_name, $institution_id, $year, $term, $email, $password, $confirm, $token, $fb_uid = null) {
		// if the form is new
		if (empty($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_NEW_ATTEMPT);
			return array(
				'error'          => null,
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'year'           => null,
				'term'           => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
			);
		} 
		// if the form token has expired
		if (!$this->validateFormToken($token)) {
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FORM_EXPIRED);
			return array(
				'error'          => self::ERROR_FORM_EXPIRED,
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'year'           => null,
				'term'           => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
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
				'error'          => self::ERROR_FORM_EMPTY,
				'first_name'     => null,
				'last_name'      => null,
				'institution_id' => null,
				'year'           => null,
				'term'           => null,
				'email'          => null,
				'password'       => null,
				'confirm'        => null,
				'token'          => $token,
			);
		}

		// check if the account is alread taken
		$has_record = $this->user_dao->read(array('account' => $email));
		if ($has_record) {
			Logger::write(self::EVENT_EMAIL_TAKEN);
			return array(
				'error'          => self::ERROR_EMAIL_TAKEN,
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'year'           => null,
				'term'           => null,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
			);
		}

		// check if the password and confirmation match
		if ($password !== $confirm) {
			return array(
				'error'          => self::ERROR_PASSWORD_NOT_MATCH,
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'year'           => null,
				'term'           => null,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
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
			$token = $this->initializeFormToken();
			Logger::write(self::EVENT_FAILED_TO_CREATE, Logger::SEVERITY_LOW);
			return array(
				'error'          => self::ERROR_FAILED_TO_CREATE,
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'institution_id' => $institution_id,
				'year'           => null,
				'term'           => null,
				'email'          => $email,
				'password'       => $password,
				'confirm'        => $confirm,
				'token'          => $token,
			);
		} 

		if (!empty($fb_uid)) {
			$this->facebook_linkage_dao->create(array(
				'user_id' => $user_id,
				'fb_uid'  => $fb_uid,
			));
		}

		$this->person_dao->create(array(
			'user_id'    => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		));

		$this->institution_linkage_dao->create(array(
			'user_id'        => $user_id,
			'institution_id' => $institution_id,
		));

		// debug
		// error_log('institution_id - ' . $institution_id . ', period - ' . $year . ', term - ' . $term);

		$this->institution_year_dao->read(array(
			'institution_id' => $institution_id,
			'period' => $year,
		));

		$this->institution_dao->read(array('id' => $institution_id));
		$institution_name = $this->institution_dao->name;

		$year_id = $this->institution_year_dao->id;

		$this->institution_term_dao->read(array(
			'institution_id' => $institution_id,
			'year_id' => $year_id,
			'name'    => ucfirst(strtolower($term)),
		));

		$term_id = $this->institution_term_dao->id;

		// debug 
		// error_log('institution_id - ' . $institution_id . ', year_id - ' . $year_id . ', term_id - ' . $term_id);

		$this->user_setting_dao->create(array(
			'user_id'        => $user_id,
			'institution_id' => $institution_id,
			'year_id'        => $year_id,
			'term_id'        => $term_id,
		));

		Logger::write(self::EVENT_NEW_USER);
		$this->unsetFormToken();
		return array(
			'success' => true,
			'user_id' => $user_id,
			'profile' => array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'institution' => $institution_name,
				'year' => $year,
				'term' => $term,
			),
			'setting' => array(
				'institution_id' => $institution_id,
				'year_id' => $year_id,
				'term_id' => $term_id,
				'fb_uid'  => $fb_uid,
			),
			'redirect'   => self::REDIRECT,
		);

		
	}

}
