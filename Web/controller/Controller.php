<?php 
/**
 * @file
 * Define the basic structures of a controller class
 */

require_once INCLUDES_PATH . '/Input.php';

 /*
 * List of methods a controller class must have.
 */
interface ControllerInterface {

	/**
	 * Mape URI to their callback function
	 *
	 * @return array
	 *  an associative array that maps URI to a call back function
	 */
	public static function path();

	/**
	 * Execute method before action is taken
	 *
	 * This method executes before the controller method does. Typically, this 
	 * includes OAuth2 authentications.
	 */
	public function beforeAction();

	/**
	 * Execute method after action is taken
	 */
	public function afterAction();


}

/**
 * This class defines methods that are commonly shared by all controllers
 */
abstract class Controller {

	protected $oauth2;
	protected $model;

	/**
	 * Default home page
	 */
	const PAGE_DEFAULT = '/welcome';
	const PAGE_HOME = '/home';

	/**
	 * Hold the data to be returned to client
	 */
	protected $output;

	function __construct() {
		// we are not doing OAuth2 at the moment.
		// $this->oauth2 = new OAuth2Model();

	}

	/**
	 * Implement Controller::beforeAction()
	 * 
	 * This is a stub!
	 */
	public function beforeAction() {

	}

	/**
	 * Implement ControllerInterface::afterAction()
	 *
	 * This is a stub!
	 */
	public function afterAction() {
	}

	/**
	 * Redirect unknown user 
	 *
	 * @param string $url
	 *  url to redirect to, default to home
	 */
	public function redirectUnknownUser($url = self::PAGE_DEFAULT) {
		if(!$this->isUserLoggedIn()) {
			$this->redirect($url);
		}

	}

	/**
	 * Handle page reedirection
	 *
	 * @param string $url
	 *  url of the page to redirect to
	 */
	protected function redirect($url) {
		header('Location: ' . $url);
		exit();
	}

	/**
	 * Get user's id
	 *
	 * @return mixed
	 *  return the user id or boolean false
	 */
	public function getUserId() {
		$user_id = Session::Get('user_id');
		if (empty($user_id)) {
			$signature  = Cookie::Get(UserSessionModel::COOKIE_SIGNATURE);
			$auto_login = Cookie::Get(USerSessionModel::COOKIE_AUTO_LOGIN); 
			if ($auto_login && !empty($signature)) {
				global $config;
				$user_cookie_dao = new UserCookieDAO(new DB($config->db));
				$user_cookie_dao->read(array('signature' => $signature));
				$user_id = $user_cookie_dao->user_id;
				if (!empty($user_id)){
					Session::Set('user_id', $user_id);
				}
			}
		}

		return $user_id == '' ? false : $user_id;
	}

	/**
	 * Get the HTTP referrer
	 */
	public function getReferrer() {
		return $_SERVER['HTTP_REFERER'];
	}

	/**
	 * Get the id of user's institution
	 * 
	 * THIS IS A STUB!!!
	 */
	public function getUserInstitutionId() {
		return 1;
	}

	/**
	 * Get the id of user's current school yesr id
	 *
	 * THIS IS A STUB!!!
	 */
	public function getUserYearId() {
		return 1;
	}

	/**
	 * Get the id of user's current school term id
	 *
	 * THIS IS A STUB!!!
	 */
	public function getUserTermId() {
		return 1;
	}

	/**
	 * Check if user is logged in
	 *
	 * This is an alias of getUserID()
	 *
	 * @return mixed
	 *  return the user id or boolean false
	 */
	public function isUserLoggedIn() {
		return $this->getUserId();
	}
	

}
