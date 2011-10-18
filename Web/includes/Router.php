<?php
/**
 * @file
 * Route request to respective controller action
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class Router {

	/**
	 * Default controller
	 *
	 * This controller is called to handle errors
	 */
	const DEFAULT_CONTROLLER = 'PageController';

	const DEFAULT_ACTION     = 'getDefaultAction';
	
	/**
	 * Manage database access
	 */
	private $db;

	/**
	 * Singleton instance
	 */
	public static $instance;

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
				$record['action'] = self::DEFAULT_ACTION;
			}

			global $config;

			$controller = new $record['controller']($config);

			$controller->action($record['action'], $params);

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
