<?php 
/**
 * @file
 * Define the basic structures of a controller class
 */

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
