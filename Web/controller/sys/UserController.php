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
	public function path() {
		return array(
			'user/register' => 'registerUser',
			'user/update'   => 'updateUser',
			'user/remove'   => 'removeUser',
			'user/profile'  => 'getUserProfile',
			'user/login'    => 'loginUser',
			'user/logout'   => 'logoutUser',
		);
	}

	/**
	 * Register a new user
	 */
	public function registerUser() {
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
	}

	/**
	 * Log out a user
	 */
	public function logoutUser() {
	}

}
