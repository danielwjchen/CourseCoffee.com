<?php
/**
 * @file
 * Log out a user
 */
class UserLogoutModel extends Model {

	/**
	 * Redirecting after successful logout attempt
	 */
	const REDIRECT = '/welcome';

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Terminate user's session
	 *
	 * @param string $user_id
	 *  the primary key that identifies a user
	 *
	 * @return array
	 *  the associattive array to redirect the user
	 */
	public function terminate($user_id) {
		return array(
			'success' => true,
			'redirect' => self::REDIRECT
		);
	}
}
