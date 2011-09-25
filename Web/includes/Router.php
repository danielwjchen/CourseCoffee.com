<?php
/**
 * @file
 * Route requests to their responding controller class
 */

class Router {

	/**
	 * @def_group error_message
	 * @{
	 * Error messages
	 */

	/**
	 * Unresolved URI
	 */
	const ERROR_404 = 'Unresolved URI';
	

	/**
	 * Manage database access
	 */
	private $db;

	/**
	 * Singleton instance
	 */
	private static $instance;

	/**
	 * 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	function __construct($config_db) {
		$this->db = new DB($config_db);
	}

	function __destruct() {
		$this->db = null;
	}

	/**
	 * Initialize the router
	 *
	 * This checks if an instance of this class already exists
	 */
	private static function Init() {
		global $config;
		if (self::$instance == null) {
			self::$instance = new static($config->db['default']);
		}
	}


	/**
	 * Scan and build a registry for the classes and their path.
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public function buildPath() {
		$this->db->perform('TRUNCATE TABLE router');
		$controllers = File::ScanDirectory(CONTROLLER_PATH, '/[a-z]+Controller\.php$/i');
		$paths = array();
		$sql = "
			INSERT INTO router
				(path, controller, action)
			VALUES
				(:path, :controller, :action)
		";
		require_once CONTROLLER_PATH . '/Controller.php';
		foreach ($controllers as $path => $object) {
			require_once $path;
			$mapping = call_user_func($object->name . '::path');
			foreach ($mapping as $uri => $action) {
				$this->db->perform($sql, array(
					'path' => $uri,
					'controller' => $object->name,
					'action' => $action,
				));
			}
		}
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * @param string $uri
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
				$record['controller'] = 'PageController';
				$record['action'] = 'get404Page';
				Logger::Write(self::ERROR_404 . ' - ' . $uri, Logger::SEVERITY_LOW);
			}
			$controller = new $record['controller'];

			call_user_func(array($controller, 'beforeAction'));
			call_user_func(array($controller, $record['action']), $params);
			call_user_func(array($controller, 'afterAction'));

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
		self::$instance->buildPath();
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * This is a factory method that creates a singleton instance
	 *
	 * @param string $uri
	 *  requested URI
	 * @param array @params
	 *  an single dimension array containing pieces exploded from original URI 
	 *  request
	 *
	 * @return string
	 *  the corresponding controller class name
	 */
	public static function Dispatch($uri, $params = array()) {
		self::Init();
		self::$instance->dispatchController($uri, $params);
	}
}
