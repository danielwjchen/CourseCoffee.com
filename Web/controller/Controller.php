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
	 * Redirect unknown user 
	 *
	 * @param string $url
	 *  url to redirect to, default to home
	 */
	public function redirectUnknownUser($url = self::PAGE_DEFAULT) {
		if(!$this->isUserLoggedIn()) {
			header('Location: '. $url);
		}

	}

	/**
	 * Check if user is logged in
	 *
	 * @return mixed
	 *  return the user id or boolean false
	 */
	public function isUserLoggedIn() {
		$user_id = Session::Get('user_id');
		// get the cookie and revive the session if session is dead.
		if (empty($user_id)) {
			$signature = Cookie::Get(UserLoginFormModel::LOGIN_COOKIE);
			if (!empty($signature)) {
				global $config;
				$user_cookie_dao = new UserCookieDAO(new DB($config->db['sys']));
				$user_cookie_dao->read(array('signature' => $signature));
				$user_id = $user_cookie_dao->user_id;
				if (!empty($user_id)){
					Session::Set('user_id', $user_id);
				}
			}
		}

		return empty($user_id) ? false : $user_id;
	}
	

	/**
	 * Implement ControllerInterface::afterAction()
	 *
	 * This is a stub!
	 */
	public function afterAction() {
	}

}
