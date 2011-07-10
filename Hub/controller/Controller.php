<?php 

/**
 * Base class of all Controllers
 */
abstract class Controller implements ControllerInterface {

	protected $oauth2;

	/**
	 * Hold the data to be returned to client
	 */
	protected $output;

	function __construct() {
		$this->oauth2 = Factory::Model('OAuth2');

	}

	/**
	 * Implement Controller::beforeAction()
	 */
	public function beforeAction() {
		$this->oauth2->verifyAccessToken();

	}

	/**
	 * Implement ControllerInterface::afterAction()
	 */
	public function afterAction() {
		print $this->output;
	}

}
