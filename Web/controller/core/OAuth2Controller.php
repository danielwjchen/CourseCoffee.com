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
	public function path() {
		return array(
			'base' => 'auth',
			'action' => array(
				'client/create' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'client/remove' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'issue/code' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'issue/token' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'issue/token-and-code' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
			),
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
	public function add() {
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
