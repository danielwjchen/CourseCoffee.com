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
       - account/email
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
 *     - tou_vid: version id for terms of use
 *     - created: timestamp of moment account created
 *     - updated: timestamp of moment account updated
 *  - class_list:
 *     - section_id: the primary key that identifies a section
 *        - section_code: a string that contains a class's subject abbreviation, 
 *          course number, and section number
 */
class UserSessionModel extends Model {

	const USER_PROFILE    = 'profile';
	const USER_SETTING    = 'setting';
	const USER_CLASS_LIST = 'class_list';

	// we difned a user has just registered by a 1 hour window
	const USER_NEWLY_REGISTERED = 3600;

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
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->user_dao         = new UserDAO($this->default_db);
		$this->user_cookie_dao  = new UserCookieDAO($this->default_db);
		$this->user_session_dao = new UserSessionDAO($this->default_db);
	}

	/**
	 * Check is the user is newly registered
	 *
	 * This is used mainly to controll access to accoount creation confirmation
	 * page.
	 *
	 * @return bool
	 */
	public function isNewlyRegistered() {
		$setting = Session::Get(self::USER_SETTING);
		if (!isset($setting['created'])) {
			return false;
		}

		if ($setting['created'] + self::USER_NEWLY_REGISTERED <= time()) {
			return false;
		}

		return true;

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
	 * Restart user session
	 *
	 * @param string $signature
	 *  a unique signature generated from user's account and password
	 */
	public function reviveUserSession($signature) {
		$has_record = $this->user_cookie_dao->read(array('signature' => $signature));
		if (!$has_record) {
			return false;
		}

		$user_id = $this->user_cookie_dao->user_id;

		$has_record = $this->user_dao->read(array('id' => $user_id));
		if (!$has_record) {
			return false;
		}

		$record = $this->user_dao->attribute;
		$this->beginUserSession($record['id'], $record['account'], $recrod['password']);
		
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

		$user_profile_dao = new UserProfileDAO($this->default_db);
		$user_profile_dao->read(array('user_id' => $user_id));
		$user_profile = $user_profile_dao->attribute;;
		$user_profile['account'] = $email;
		unset($user_profile['id']);

		// debug
		// error_log('user_profile - ' . print_r($user_profile, true));

		$this->setUserProfile($user_profile);

		$user_setting_dao = new UserSettingDAO($this->default_db);
		$user_setting_dao->read(array('user_id' => $user_id));
		$user_setting = $user_setting_dao->attribute;;
		$fb_linkage_dao = new UserFacebookLinkageDAO($this->default_db);
		$fb_linkage_dao->read(array('user_id' => $user_id));
		$user_setting['fb_uid'] = $fb_linkage_dao->fb_uid;

		unset($user_setting['id']);
		unset($user_setting['user_id']);

		// debug
		// error_log('user_setting - ' . print_r($user_setting, true));

		$this->setUserSetting($user_setting);

		$user_class_list_dao = new UserClassListDAO($this->institution_db);
		$user_class_list_dao->read(array(
			'user_id'        => $user_id,
			'institution_id' => $user_setting['institution_id'],
			'year_id'        => $user_setting['year_id'],
			'term_id'        => $user_setting['term_id'],
		));
		$record = $user_class_list_dao->list;;

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
		Cookie::Del(self::COOKIE_SIGNATURE);
		Cookie::Set(self::COOKIE_AUTO_LOGIN, 'false');
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
	 * Get the sub domain of the user in session
	 *
	 * @return string
	 */
	public function getUserDomain() {
		$profile = Session::Get(self::USER_PROFILE);
		if (isset($profile['institution_domain'])) {
			return $profile['institution_domain'];
		} else {
			return false;
		}
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
