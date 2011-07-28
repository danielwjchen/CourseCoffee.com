<?php
/**
 * @file
 * Handle user creation and management
 */

class UserController extends Controller implements ControllerInterface {

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
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
	 */
	public function registerUser() {
		$token    = Input::Post('token');
		$email    = Input::Post('email', FILTER_SANITIZE_EMAIL);
		$password = Input::Post('password');
		$confirm  = Input::Post('confirm');

		$user_register_form = new UserRegisterFormModel();

		$result = $user_register_form->processForm(
			$email, 
			$password, 
			$confirm, 
			$token
		);
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
