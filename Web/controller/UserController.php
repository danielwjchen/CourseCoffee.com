<?php
/**
 * @file
 * Handle user creation and management
 */

class UserController extends Controller implements ControllerInterface {

	/**
	 * Access to user session values
	 */
	private $user_session_model;

	/**
	 * Handle output in JSON format
	 */
	private $output;

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_session_model = new UserSessionModel();
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'user-register-facebook' => 'registerUserByFB',
			'user-register-regular'  => 'registerUserByUs',
			'user-update'   => 'updateUser',
			'user-remove'   => 'removeUser',
			'user-profile'  => 'getUserProfile',
			'user-login-fb' => 'loginUserByFB',
			'user-login'    => 'loginUser',
			'user-logout'   => 'logoutUser',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {

	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function afterAction() {
		echo $this->output->render();
	}

	/**
	 * Check if the user qualifies for auto login
	 */
	private function checkAutoLogin() {
		return Cookie::Get(USerSessionModel::COOKIE_AUTO_LOGIN); 
	}


	private function registerUser($fist_name, $last_name, $institution_id, $section_id, $course_code, $year, $term, $token, $password, $confirm) {
	}

	/**
	 * Register user via facebook
	 *
	 * Since this is our first semester, some of information is hard-coded
	 */
	public function registerUserByFB() {
		$fb_model = new FBModel();

		// debug
		//error_log(__METHOD__ . ' : $_REQUEST - ' . print_r($_REQUEST, true));

		$result = $fb_model->processSignUpRequest($_REQUEST['signed_request']);

		if (isset($result['success'])) {

			// debug 
			// error_log(__METHOD__ . ' : result - ' . print_r($result, true));

			$user_register_form = new UserRegisterFormModel();
			$first_name     = $result['first_name'];
			$last_name      = $result['last_name'];
			$institution_id = $result['school'];
			$section_id     = Session::Get('section_id');
			$course_code    = Session::Get('course_code');
			$year           = '2011';
			$term           = 'fall';
			$email          = $result['email'];
			$password       = $result['password'];
			$fb_uid         = $result['fb_uid'];
			$user_record = $user_register_form->createUserAccount(
				$first_name, 
				$last_name, 
				$institution_id, 
				$year, 
				$term, 
				$email, 
				$password,
				$fb_uid
			);

			if (isset($user_record['error'])) {
				$this->redirect($user_record['redirect']);
			}

			// debug 
			// error_log(__METHOD__ . ' : user_record - ' . print_r($user_record, true));

			$this->user_session_model->setUserSessionCookie($user_record['user_id'], $email, $password);
			$this->user_session_model->setUserProfile($user_record['profile']);
			$this->user_session_model->setUserSetting($user_record['setting']);

			$class_list = array();
			if (!empty($section_id) && !empty($course_code)) {
				Session::Del('section_id');
				Session::Del('course_code');

				$user_enroll_class_model = new UserEnrollClassModel();
				$user_enroll_class_model->createLinkage(
					$user_record['user_id'], 
					$section_id
				);
				$class_list = array(
					'section_id' => $section_id, 
					'course_code' => $course_code
				);
			}
			$this->user_session_model->setUserClassList($class_list);

			unset($user_record['profile']);
			unset($user_record['setting']);
		}

		$this->clientRedirect($result['redirect']);

	}

	/**
	 * Register user via our own registration process
	 *
	 * Since this is our first semester, some of information is hard-coded
	 */
	public function registerUserByUs() {
		$first_name     = Input::Post('first-name');
		$last_name      = Input::Post('last-name');
		$institution_id = Input::Post('school');
		$section_id     = Session::Get('section_id');
		$course_code    = Session::Get('course_code');
		$year           = '2011';
		$term           = 'fall';
		$token          = Input::Post('token');
		$email          = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password       = Input::Post('password');
		$confirm        = Input::Post('confirm');

		$user_register_form = new UserRegisterFormModel();

		$result = $user_register_form->processForm(
			$first_name,
			$last_name,
			$institution_id,
			$year,
			$term,
			$email, 
			$password, 
			$confirm, 
			$token
		);
		
		if (isset($result['success'])) {
			$this->user_session_model->setUserSessionCookie($result['user_id'], $email, $password);
			$this->user_session_model->setUserProfile($result['profile']);
			$this->user_session_model->setUserSetting($result['setting']);

			$class_list = array();
			if (!empty($section_id) && !empty($course_code)) {
				Session::Del('section_id');
				Session::Del('course_code');

				$user_enroll_class_model = new UserEnrollClassModel();
				$user_enroll_class_model->createLinkage(
					$result['user_id'], 
					$section_id
				);
				$class_list = array(
					'section_id' => $section_id, 
					'course_code' => $course_code
				);
			}
			$this->user_session_model->setUserClassList($class_list);

			unset($result['profile']);
			unset($result['setting']);

		}

		$this->output = new JSONView($result);
	}

	/**
	 * Update user's information
	 */
	public function updateUser() {
	}

	/**
	 * Remove a user's account
	 */
	public function removeUser() {
	}

	/**
	 * Get a user's profile information
	 */
	public function getUserProfile() {
	}

	/**
	 * Accept user login request by facebook and begin session
	 */
	public function loginUserByFB() {
		if (!$this->checkAutoLogin()) {
			$this->output = new JSONView(array('error' => true));
			return ;
		}
		$fb_uid = Input::Post('fb_uid');
		$user_login_model = new UserLoginFormModel();
		$result = $user_login_model->processFBLogInRequest($fb_uid);
		// begin user session on success
		if (isset($result['success'])) {
			$this->user_session_model = new UserSessionModel();
			$this->user_session_model->beginUserSession(
				$result['user_id'], 
				$result['email'], 
				Crypto::Encrypt($result['password'])
			);
		}

		// debug
		// error_log( __METHOD__ . ' : result - ' . print_r($result, true));

		$this->output = new JSONView($result);
	}

	/**
	 * Accept user login request and begin session
	 */
	public function loginUser() {
		$login_form   = new UserLoginFormModel();

		$email    = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password = Input::Post('password');
		$token    = Input::Post('token');

		$result = $login_form->processForm($email, $password, $token);

		// begin user session on success
		if (isset($result['success'])) {
			$this->user_session_model = new UserSessionModel();
			$this->user_session_model->beginUserSession($result['user_id'], $email, Crypto::Encrypt($password));
		}

		$this->output = new JSONView($result);
	}

	/**
	 * Log out a user
	 */
	public function logoutUser() {
		$logout_model = new UserLogoutModel();
		$user_id = $this->user_session_model->getUserId();
		$result = $logout_model->terminate($user_id);

		if (isset($result['success'])) {
			$this->user_session_model->endUserSession();
		}

		$this->output = new JSONView($result);
	}

}
