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
 *     - institution_uri
 *     - year
 *     - term
 *  - setting
 *     - institution_id
 *     - year_id
 *     - term_id
 *     - fb_uid
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
	 * Name to be used for the cookie
	 */
	const COOKIE_SIGNATURE  = 'lc';
	const COOKIE_AUTO_LOGIN = 'al';

	/**
	 * @defgroup dao
	 * @{
	 */
	private $user_dao;
	private $user_session_dao;
	private $user_cookie_dao;
	/**
	 * @} End of "dao"
	 */

	/**
	 * Extend Model::construct()
	 */
	function __construct() {
		parent::__construct();
		$this->user_cookie_dao = new UserCookieDAO($this->db);
		$this->user_session_dao = new UserSessionDAO($this->db);
	}

	/**
	 * Set cookie for user session
	 *
	 * @param string $user_id
	 *  the user's id
	 * @param string $email
	 *  a string of email address to be user as account
	 * @param string $password
	 *  a string to identifer the user as the owner of the account
	 */
	public function setUserSessionCookie($user_id, $email, $password) {
		Session::Set('user_id', $user_id);
		$signature = Crypto::encrypt($email . $password);
		$this->user_cookie_dao->create(array(
			'user_id'   => $user_id,
			'signature' => $signature, 
		));

		Cookie::Set(self::COOKIE_SIGNATURE, $signature, Cookie::EXPIRE_MONTH);
		Cookie::Set(self::COOKIE_AUTO_LOGIN, true, Cookie::EXPIRE_MONTH);
	}

	/**
	 * Begin user session
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
	public function beginUserSession($user_id, $email, $password) {
		$this->setUserSessionCookie($user_id, $email, $password);

		$user_profile_dao = new UserProfileDAO($this->db);
		$user_profile_dao->read(array('user_id' => $user_id));
		$user_profile = $user_profile_dao->attribute;;
		unset($user_profile['id']);

		// debug
		// error_log('user_profile - ' . print_r($user_profile, true));

		$this->setUserProfile($user_profile);

		$user_setting_dao = new UserSettingDAO($this->db);
		$user_setting_dao->read(array('user_id' => $user_id));
		$user_setting = $user_setting_dao->attribute;;
		$fb_linkage_dao = new UserFacebookLinkageDAO($this->db);
		$fb_linkage_dao->read(array('user_id' => $user_id));
		$user_setting['fb_uid'] = $fb_linkage_dao->fb_uid;

		unset($user_setting['id']);
		unset($user_setting['user_id']);

		// debug
		// error_log('user_setting - ' . print_r($user_setting, true));

		$this->setUserSetting($user_setting);

		$user_class_list_dao = new UserClassListDAO($this->db);
		$user_class_list_dao->read(array(
			'user_id'        => $user_id,
			'institution_id' => $user_setting['institution_id'],
			'year_id'        => $user_setting['year_id'],
			'term_id'        => $user_setting['term_id'],
		));
		$record = $user_class_list_dao->list;;
		// another ugly hack! 
		if (isset($record['section_id'])) {
			$record = array($record);
		}

		// debug
		// error_log(__METHOD__ . 'user class list record- ' . print_r($record, true));

		$class_list = array();
		if (!empty($record)) {
			foreach ($record as $item) {
				$class_list[$item['section_id']] = $item['section_code'];
			}
		}

		// debug
		// error_log(__METHOD__ . 'user class list- ' . print_r($class_list, true));

		$this->setUserClassList($class_list);
	}

	/**
	 * Unset all values in user session.
	 *
	 * Not sure why we have this, but we do.
	 */
	public function flushUserSession() {
		Session::Del('user_id');
		Session::Del(self::USER_PROFILE);
		Session::Del(self::USER_SETTING);
		Session::Del(self::USER_CLASS_LIST);
	}

	/**
	 * End a user session
	 *
	 * This is called when a user logs off, for example
	 */
	public function endUserSession() {
		$user_id = Session::Get('user_id');
		$this->user_cookie_dao->read(array('user_id' => $user_id));
		$this->user_cookie_dao->destroy();
		Cookie::del(self::COOKIE_SIGNATURE);
		Cookie::del(self::COOKIE_AUTO_LOGIN);
		session_destroy();
	}

	/**
	 * Get the id of the user currently in session
	 *
	 * @return int
	 */
	public function getUserId() {
		return Session::Get('user_id');
	}

	/**
	 * Get user's fb_uid if there is one
	 */
	public function getFbUserId() {
		$setting = Session::Get(self::USER_SETTING);

		return $setting['fb_uid'];
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
		return Session::Get(self::USER_PROFILE);
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
		Session::Set(self::USER_PROFILE, $profile);
	}

	/**
	 * Set the value for a specific field in user's profile
	 */
	public function setUserProfileField($field, $value) {
		$profile = Session::Get(self::USER_PROFILE);
		$profile[$field] = $value;
		Session::Set(self::USER_PROFILE, $profile);
	}

	/**
	 * Set the values in user's setting
	 *
	 * @param array $profile
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 *  - fb_uid
	 */
	public function setUserSetting(array $setting) {
		Session::Set(self::USER_SETTING, $setting);
	}

	/**
	 * Get the user's profile
	 *
	 * @return array
	 *  - institution_id
	 *  - year_id
	 *  - term_id
	 *  - fb_uid
	 */
	public function getUserSetting() {
		return Session::Get(self::USER_SETTING);
	}

	/**
	 * Set the values in user's class list
	 *
	 * @param array $class_list
	 *  - section_id:
	 *     - section_code:
	 */
	public function setUserClassList(array $class_list) {
		Session::Set(self::USER_CLASS_LIST, $class_list);
	}

	/**
	 * Set a particular item in user's class list
	 *
	 * @param array $item
	 *  - section_id:
	 *     - section_code:
	 */
	public function setUserClassListFiled($item) {
		$class_list = Session::Get(self::USER_CLASS_LIST);
		$class_list = array_merge($class_list, $item);
		Session::Set(self::USER_CLASS_LIST, $class_list);
	}

	/**
	 * Get the user's class list
	 *
	 * @return array
	 *  - section_id:
	 *     - section_code:
	 */
	public function getUserClassList() {
		return Session::Get(self::USER_CLASS_LIST);
	}

}
