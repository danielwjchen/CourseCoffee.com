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
	 * Initialize controller object
	 */
	public static function Init();

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
	 * Build and store URI routing
	 */
	public static function Build();

	/**
	 * Dispatch controller object and call method according to request
	 */
	public static function Dispatch();

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
class Controller {

	/**
	 * Singleton instance
	 */
	public static $instance;

	/**
	 * Default controller
	 *
	 * This controller is called to handle errors
	 */
	const DEFAULT_CONTROLLER = 'PageController';
	
	/**
	 * Manage database access
	 */
	private $db;

	function __construct($config) {
		$this->db = new DB($config->db['default']);
	}

	function __destruct() {
		$this->db = null;
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

	/**
	 * Scan and build a registry for the classes and their path.
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public function buildControllerRegistry() {
		$this->db->perform('TRUNCATE TABLE router');
		$controllers = File::Scan(MODULES_PATH, '/[a-z]+Controller\.php$/i');
		$paths = array();
		$sql = "
			INSERT INTO router
				(path, controller, action)
			VALUES
				(:path, :controller, :action)
		";
		require_once INCLUDES_PATH . '/modules/Controller.php';
		foreach ($controllers as $path => $object) {
			require_once $path;
			$routing = call_user_func($object->name . '::Route');
			foreach ($routing as $action => $paths) {
				foreach ($paths as $path) {
					$this->db->perform($sql, array(
						'path' => $path,
						'controller' => $object->name,
						'action' => $action,
					));
				}
			}
		}
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * @param string $path
	 *  requested URI
	 * @param array @params
	 *  an single dimension array containing pieces exploded from original URI 
	 *  request
	 *
	 * @return string
	 *  the corresponding controller class name
	 */
	public function dispatchController($uri, $params = array()) {
		try {
			$sql = "SELECT controller, action FROM router WHERE path = :uri";
			$record = $this->db->fetch($sql, array('uri' => $uri)); 

			// if we can't resolve a controller action from the uri
			if (empty($record)) {
				$record['controller'] = self::DEFAULT_CONTROLLER;
				$record['action'] = 'get404Page';
				Logger::Write($record['controller']::ERROR_404 . ' - ' . $uri, Logger::SEVERITY_LOW);
			}

			$controller = new $record['controller']($config);

			//call_user_func(array($controller, 'beforeAction'));
			call_user_func_array(array($controller, $record['action']), $params);
			$controller->afterAction();

		} catch (Exception $e) {
			Logger::Write($e->getMessage(), Logger::SEVERITY_LOW);
		}
	}

	/**
	 * Scan and build a path map that route requests to the respective actions
	 *
	 * This is a factory method that creates a singleton instance
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public static function Build() {
		self::Init();
		self::$instance->buildControllerRegistry();
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * This is a factory method that creates a singleton instance
	 */
	public static function Dispatch() {
		$params = array();
		if (isset($_GET['q'])) {
			$request = explode('/', $_GET['q']);
			$uri = array_shift($request);
			$params = $request;

			// debug
			// error_log('uri - ' .$uri);
			// error_log('params - ' . print_r($params, true));

		}

		self::Init();
		self::$instance->dispatchController($uri, $params);
	}

}
