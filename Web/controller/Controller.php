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

	protected $user_session;
	protected $domain;
	protected $sub_domain;
	protected $supported_domain;
	protected $output;

	/**
	 * Default home page
	 */
	const PAGE_DEFAULT   = '/welcome';
	const PAGE_HOME      = '/home';
	const DEFAULT_DOMAIN = 'www';

	function __construct() {
		global $config;
		$this->domain       = $config->domain;
		$this->sub_domain   = $this->getSubDomain();
		$sub_domain = $config->db['institution'];
		$this->supported_domain = array_flip(array_keys($sub_domain));

		if (array_key_exists($this->getRequestedSubDomain(), $this->supported_domain)) {
			$this->user_session = new UserSessionModel($this->sub_domain);
		}

	}

	/**
	 * Implement Controller::beforeAction()
	 *
	 *  this is a stub!
	 */
	public function beforeAction() {

	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		echo $this->output->render();
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
	 * Redirect unknow domain
	 */
	protected function redirectUnsupportedDomain() {
		$requested   = $this->getRequestedSubDomain();
		$user_domain = $this->getSubDomain();
		$url = $this->getProtocol();
		if (!array_key_exists($requested, $this->supported_domain)) {
			if (array_key_exists($user_domain, $this->supported_domain)) {
				$url .= $user_domain . '.' . $this->domain;
				$this->redirect($url);
			}
			$this->redirect($url . self::DEFAULT_DOMAIN . '.' . $this->domain);
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
	 *
	 */
	protected function clientRedirect($url) {
		echo <<<HTML
<script type="text/javascript">
	top.location.href='{$url}';
</script>
HTML;
		exit();
	}

	/**
	 * Get the requested protocol
	 */
	protected function getProtocol() {
		return empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
	}

	/**
	 * Get user's id
	 *
	 * @return mixed
	 *  return the user id or boolean false
	 */
	public function getUserId() {
		if (!is_object($this->user_session)) {
			return false;
		}
		$user_id = $this->user_session->getUserId();
		if (empty($user_id)) {
			$signature  = Cookie::Get(UserSessionModel::COOKIE_SIGNATURE);
			$auto_login = Cookie::Get(UserSessionModel::COOKIE_AUTO_LOGIN); 
			if ($auto_login != 'false' && !empty($signature)) {
				$result = $this->user_session->reviveUserSession($signature);
				if (!$result) {
					return false;
				}

				$user_id = $this->user_session->getUserId();

			}
		}
		return $user_id == '' ? false : $user_id;
	}

	/**
	 * Get requested domain
	 */
	protected function getRequestedDomain() {
		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * Get current sub-domain
	 */
	protected function getRequestedSubDomain() {
		return str_replace('.', '', str_replace($this->domain, '', $_SERVER['SERVER_NAME']));
;
	}

	/**
	 * Get Subdomain
	 *
	 * @return mixed
	 */
	protected function getSubDomain() {
		$domain = null;
		if (is_object($this->user_session)) {
			$domain = $this->user_session->getUserDomain();
		}
		if (empty($domain)) {
			$domain = $this->getRequestedSubDomain();
		}
		return $domain;
	}

	/**
	 * Get the HTTP referrer
	 */
	public function getReferrer() {
		return $_SERVER['HTTP_REFERER'];
	}

	/**
	 * Get institution id
	 * 
	 * THIS IS A STUB!!!
	 */
	public function getInstitutionId() {
		$domain = $this->getRequestedSubDomain();
		$college = new CollegeModel($this->sub_domain);
		$record = $college->getCollegeByDomain($domain);
		return $record['id'];
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
	 * @return bool
	 */
	public function isUserLoggedIn() {
		$user_id = $this->getUserId();
		return !empty($user_id);
	}
	

}
