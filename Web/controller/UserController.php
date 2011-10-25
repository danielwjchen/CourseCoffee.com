<?php
/**
 * @file
 * Handle user creation and management
 */

class UserController extends Controller implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'user-register-facebook' => 'registerUserByFB',
			'user-register-regular'  => 'registerUserByUs',
			'user-link-facebook' => 'linkUserWithFacebook',
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
		$this->redirectUnsupportedDomain();

	}

	/**
	 * Check if the user qualifies for auto login
	 */
	private function checkAutoLogin() {
		return Cookie::Get(UserSessionModel::COOKIE_AUTO_LOGIN); 
	}

	/**
	 * Register user
	 *
	 * This is a helper function to share code among registerUserByFB() and
	 * registerUserByUs()
	 */
	private function processRegistration($user_record, $section_id) {
		$this->user_session->setUserSessionCookie($user_record['user_id'], $email, $password);
		$this->user_session->setUserProfile($user_record['profile']);
		$this->user_session->setUserSetting($user_record['setting']);

		$class_list = array();
		if (!empty($section_id)) {
			Session::Del('section_id');
			$college_class = new CollegeClassModel($this->sub_domain);
			$class_info = $college_class->getClassById($section_id);

			// check if the requested section id is valid
			if (isset($class_info['content'])) {

				$user_enroll_class_model = new UserEnrollClassModel($this->sub_domain);
				$user_enroll_class_model->createLinkage(
					$user_record['user_id'], 
					$section_id
				);
				$class_list[$section_id] = $class_info['content']['section_code'];
			}
			$this->user_session->setUserClassList($class_list);


		}
	}

	/**
	 * Register user via facebook
	 *
	 * Since this is our first semester, some of information is hard-coded
	 */
	public function registerUserByFB() {
		$fb_model = new FBModel($this->sub_domain);

		// debug
		//error_log(__METHOD__ . ' : $_REQUEST - ' . print_r($_REQUEST, true));

		$result = $fb_model->processSignUpRequest($_REQUEST['signed_request']);

		if (isset($result['success'])) {

			// debug 
			// error_log(__METHOD__ . ' : result - ' . print_r($result, true));

			$user_register_form = new UserRegisterFormModel($this->sub_domain);
			$tou_model = new TermsOfUseModel($this->sub_domain);
			$tou_vid        = $tou_model->getLatest();
			$first_name     = $result['first_name'];
			$last_name      = $result['last_name'];
			$institution_id = $this->getInstitutionId();
			$section_id     = Session::Get('section_id');
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
				$tou_vid,
				$fb_uid
			);

			if (isset($user_record['success'])) {
				$this->processRegistration($user_record, $section_id);
			}
		}

		$this->clientRedirect($user_record['redirect']);

	}

	/**
	 * Register user via our own registration process
	 *
	 * Since this is our first semester, some of information is hard-coded
	 */
	public function registerUserByUs() {
		$tou_model = new TermsOfUseModel($this->sub_domain);
		$tou_vid        = $tou_model->getLatest();
		$first_name     = Input::Post('first-name');
		$last_name      = Input::Post('last-name');
		$institution_id = $this->getInstitutionId();
		$section_id     = Session::Get('section_id');
		$year           = '2011';
		$term           = 'fall';
		$token          = Input::Post('token');
		$email          = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password       = Input::Post('password');
		$confirm        = Input::Post('confirm');

		$user_register_form = new UserRegisterFormModel($this->sub_domain);

		$user_record = $user_register_form->processForm(
			$first_name,
			$last_name,
			$institution_id,
			$year,
			$term,
			$email, 
			$password, 
			$confirm, 
			$tou_vid,
			$token
		);
		
		if (isset($user_record['success'])) {
			$this->processRegistration($user_record, $section_id);
		}

		unset($user_record['profile']);
		unset($user_record['setting']);

		$this->output = new JSONView($user_record);
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
		if ($this->checkAutoLogin() == 'false') {
			$this->output = new JSONView(array('error' => true));
			return ;
		}
		$fb_uid = Input::Post('fb_uid');
		$user_login_model = new UserLoginFormModel($this->sub_domain);
		$result = $user_login_model->processFBLogInRequest($fb_uid);
		// begin user session on success
		if (isset($result['success'])) {
			$this->user_session->beginUserSession(
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
		$login_form = new UserLoginFormModel($this->sub_domain);

		$email    = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password = Input::Post('password');
		$token    = Input::Post('token');

		$result = $login_form->processForm($email, $password, $token);

		// begin user session on success
		if (isset($result['success'])) {
			$this->user_session->beginUserSession($result['user_id'], $email, Crypto::Encrypt($password));
		}

		$this->output = new JSONView($result);
	}

	/**
	 * Link user with facebook
	 *
	 * At the moment, this methd refreshes the page regardlessly
	 */
	public function linkUserWithFacebook() {
		$user_id = $this->getUserId();
		$fb_uid  = Input::Post('fb_uid');
		$result['redirect'] = '/home';
		$fb_model = new FBModel($this->sub_domain);
		if ($fb_model->processFBLinkageRequest($fb_uid, $user_id)) {
			$this->user_session->setFbUserId($fb_uid);
		}
		$this->output = new JSONView($result);
	}

	/**
	 * Log out a user
	 */
	public function logoutUser() {
		$logout_model = new UserLogoutModel($this->sub_domain);
		$user_id = $this->getUserId();
		$result = $logout_model->terminate($user_id);

		$this->user_session->endUserSession();

		$this->output = new JSONView($result);
	}

}
