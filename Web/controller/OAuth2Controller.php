<?php
/**
 * @file
 * Handle OAuth2 client/app creation and authorization
 */

class OAuth2Controller extends Controller implements ControllerInterface{

	/**
	 * the default view class
	 */
	private $view;

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->view = Factory::View('OAuth2');
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'auth/client/create'        => 'addClient',
			'auth/client/remove'        => 'removeClient',
			'auth/issue/code'           => 'issueCode',
			'auth/issue/token'          => 'issueToken',
			'auth/issue/token-and-code' => 'issueTokenAndCode',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {
	}

	/**
	 * Add new client/app
	 */
	public function addClient() {
		if (isset($_POST['client_id']) && isset($_POST['client_secret'])) {
			$this->oauth2->addClient(
				$_POST['client_id'], 
				$_POST['client_secret'],
				'http://hub.plnnr.dev/oauth2/issueToken'
			);
			$_SESSION['client_id'] = $_POST['client_id'];
			$_SESSION['client_secret'] = $_POST['client_secret'];
			$this->oauth2->finishClientAuthorization(true, array(
				'response_type' => 'code',
				'client_id' => $_POST['client_id'],
				'redirect_uri' => 'http://hub.plnnr.dev/oauth2/listClient',
			));

		} else {
			$this->view->addClient();

		}

	}

	/**
	 * Remove client/app
	 */
	public function remove() {
	}

	/**
	 * list client
	 */
	public function listClient() {
		$this->view->listClient(
			$_SESSION['client_id'], 
			$_SESSION['client_secret'],
			$_GET['code']
		);
	}

	/**
	 * Issue token to grant client/app access to data
	 */
	public function issueToken() {
		$this->oauth2->grantAccessToken();
	}

	/**
	 * Issue code to grant client/app access to data
	 */
	public function issueCode() {
	}

	/**
	 * Issue code and token to grant client/app access to data
	 */
	public function issueTokenAndCode() {
	}
}
