<?php 
/**
 * @file
 * Define the basic structures of a controller class
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
require_once INCLUDES_PATH . '/Input.php';

 /*
 * List of methods a controller class must have.
 */
interface ControllerInterface {

	/**
	 * Initialize controller object
	 */
	public static function Init();

	/**
	 * Get default action
	 *
	 * This is called when a request cannot be resolved
	 */
	public function getDefaultAction();

	/**
	 * Route URL paths to controller methods
	 *
	 * @return array
	 *  an associative array that maps URI to a call back function
	 *   - method: a controller method that handles the request
	 *      - path: an array of paths
	 */
	public static function Route();

	/**
	 * Respond to request and execute callback
	 *
	 * @param string callback
	 *  controller method to be executed
	 * @param array params
	 */
	public function action($callback, array $params = null);

}

/**
 * This class defines methods that are commonly shared by all controllers
 */
class Controller {

	function __construct($config) {
		$this->domain = $config->domain;
	}

	/**
	 * Get the HTTP referrer
	 */
	protected function getReferrer() {
		return $_SERVER['HTTP_REFERER'];
	}

	/**
	 * Get the requested protocol
	 */
	protected function getProtocol() {
		return empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
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
		return str_replace(
			'.', 
			'', 
			str_replace($this->domain, '', $_SERVER['SERVER_NAME'])
		);
	}

	/**
	 * Initialize Controller
	 *
	 * This checks if an instance of this class already exists
	 */
	public static function Init() {
		global $config;
		if (self::$instance == null) {
			self::$instance = new static($config);
		}
	}

}
