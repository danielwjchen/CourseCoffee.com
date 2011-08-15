<?php
/**
 * @file
 * Manage user session
 *
 * If you access user's session data with $_SESSION or Session::Get() and 
 * Session::Set(), you are in big trouble my friend.
 *
 * Data structure:
 *  - user_id
 *  - signature
 *  - profile
 *     - first_name
 *     - last_name
 *     - institution
 *     - year
 *     - term
 *  - setting
 *     - institution_id
 *     - year_id
 *     - term_id
 *  - class_list:
 *     - section_id: the primary key that identifies a section
 *        - section_code: a string that contains a class's subject abbreviation, 
 *          course number, and section number
 */
class UserSessionModel extends Model {

	const USER_PROFILE    = 'profile';
	const USER_SETTING    = 'setting';
	const USER_CLASS_LIST = 'class_list';

	/**
	 * Start user session
	 *
	 * we drop a cookie so we can automatically log in when the user comes 
	 * back. Why are we not using user's id? because if the user changes his
	 * password, this will force him to re-login when accessing from other 
	 * browsers
	 *
	 * @param string $user_id
	 *  the user's id
	 * @param string $email
	 *  a string of email address to be user as account
	 * @param string $password
	 *  a string to identifer the user as the owner of the account
	 */
	public function beginUserSession($user_id. $email, $password) {
	}

	/**
	 * Populate user session
	 *
	 * This is called after a user register for an account, typically.
	 *
	 * @param int $user_id
	 * @param string $email
	 * @param string $password
	 * @param array $profile
	 *  - first_name
	 *  - last_name
	 *  - institution
	 *  - year
	 *  - term
	 * @param array $setting
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 * @param array $class_list
	 *  - section_id: the primary key that identifies a section
	 *     - section_code: a string that contains a class's subject abbreviation, 
	 *       course number, and section number
	 */
	public function populateUserSession($user_id, $email, $password, array $profile, array $setting, array $class_list = array()) {
	}

	/**
	 * Unset all values in user session.
	 *
	 * Not sure why we have this, but we do.
	 */
	public function flushUserSession() {
	}

	/**
	 * End a user session
	 *
	 * This is called when a user logs off, for example
	 */
	public function endUserSession() {
	}

	/**
	 * Get the user's profile
	 *
	 * @return array
	 *  - first_name
	 *  - last_name
	 *  - institution
	 *  - year
	 *  - term
	 */
	public function getUserProfile() {
	}

	/**
	 * Set the values in user's profile
	 *
	 * @param array $profile
	 *  - first_name
	 *  - last_name
	 *  - institution
	 *  - year
	 *  - term
	 */
	public function setUserProfile(array $profile) {
	}

	/**
	 * Set the value for a specific field in user's profile
	 */
	public function setUserProfileField($field, $value) {
	}

	public function setUserSetting() {
	}
	public function getUserSetting() {
	}
	public function getUserSettingFiled() {
	}

	public function setUserClassList() {
	}
	public function getUserClassList() {
	}
	public function getUserClassListFiled() {
	}

}
