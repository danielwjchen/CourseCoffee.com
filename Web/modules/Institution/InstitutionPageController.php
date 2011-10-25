<?php
/**
 * @file
 * Handle institution related requests
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class InstitutionPageController extends PageController implements ControllerInterface {

	/**
	 * Default home page
	 */
	const PAGE_DEFAULT   = '/portal';
	const PAGE_PORTAL    = '/portal';
	const PAGE_WELCOME   = '/welcome';
	const PAGE_HOME      = '/home';
	const DEFAULT_DOMAIN = 'www';


	protected $user_session;
	protected $sub_domain;
	protected $supported_domain;

	function __construct($config) {
		parent::__construct($config);
		$this->sub_domain   = $this->getSubDomain();
		$sub_domain = $config->db['institution'];
		$this->supported_domain = array_flip(array_keys($sub_domain));

		if (array_key_exists($this->getRequestedSubDomain(), $this->supported_domain)) {
			$this->user_session = new UserSessionModel($this->sub_domain);
		}
	}

	/**
	 * Redirect unknown user 
	 *
	 * @param string $url
	 *  url to redirect to, default to home
	 */
	protected function redirectUnknownUser($url = self::PAGE_WELCOME) {
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
			$this->redirect($url . self::DEFAULT_DOMAIN . '.' . $this->domain . self::PAGE_PORTAL);
		}

	}

	/**
	 * Get user's id
	 *
	 * @return mixed
	 *  return the user id or boolean false
	 */
	protected function getUserId() {
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
	 * Get institution id
	 * 
	 */
	protected function getInstitutionId() {
		$domain = $this->getRequestedSubDomain();
		$institution = new InstitutionModel($this->sub_domain);
		$record = $institution->getInstitutionByDomain($domain);
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
	

	/**
	 * Override PageController::Route()
	 */
	public static function Route() {
		return array(
			'getPortalPage' => array(
				'portal',
			),
		);
	}

	/**
	 * Get portal page
	 *
	 * The portal page serves as the redirect page when there is no requested 
	 * subdomain/campus, or the request does not map to a campus site
	 */
	public function getPortalPage() {
		$model = new InstitutionListModel($this->sub_domain);
		$this->output = new InstitutionPortalPageView(array(
			'institution_list' => $model->getInstitutionList(),
			'domain' => $this->domain,
		));
	}
}
