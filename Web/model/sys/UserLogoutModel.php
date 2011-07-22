<?php
/**
 * @file
 * Log out a user
 */
class UserLogoutModel extends Model {

	/**
	 * Access to user record
	 */
	private $user_dao;

	/**
	 * Access to user cookie record
	 */
	private $user_cookie_dao;

	/**
	 * Redirecting after successful logout attempt
	 */
	const REDIRECT = '/welcome';

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_dao = new UserDAO($this->db);
		$this->user_cookie_dao = new UserCookieDAO($this->db);
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
		$this->user_cookie_dao->read(array('user_id' => $user_id));
		$this->user_cookie_dao->destroy();
		Cookie::del(UserLoginFormModel::LOGIN_COOKIE);
		Session::del('user_id');
		return array('redirect' => self::REDIRECT);
	}
}
