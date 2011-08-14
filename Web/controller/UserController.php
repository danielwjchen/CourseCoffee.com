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
			'user-register-facebook' => 'registerUserByFacebook',
			'user-register' => 'registerUser',
			'user-update'   => 'updateUser',
			'user-remove'   => 'removeUser',
			'user-profile'  => 'getUserProfile',
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
	}

	/**
	 * Register a new user
	 *
	 * Since this is our first semester, some of information is hard-coded
	 */
	public function registerUser() {
		$first_name     = Input::Post('first-name');
		$last_name      = Input::Post('last-name');
		$institution_id = Input::Post('school');
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
			$user_login = new UserLoginFormModel();
			$user_login->startUserSession(
				$result['user_id'], 
				$result['email'], 
				$result['password']
			);
		}
		$json = new JSONView($result);
		echo $json->render();
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
	 * Log in a user
	 */
	public function loginUser() {
		$login_form = new UserLoginFormModel();

		$email    = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password = Input::Post('password');
		$token    = Input::Post('token');

		$result = $login_form->processForm($email, $password, $token);
		if (isset($result['success'])) {
		}

		$json = new JSONView($result);
		echo $json->render();
	}

	/**
	 * Log out a user
	 */
	public function logoutUser() {
		$logout = new UserLogoutModel();
		$user_id = Session::Get('user_id');
		$result = $logout->terminate($user_id);
		$json = new JSONView($result);
		echo $json->render();
	}

}
