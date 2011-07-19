<?php
/**
 * @file
 * Route requests to their responding controller class
 */

class Router {

	/**
	 * Manage database access
	 */
	private static $db;

	/**
	 * Initialize the router
	 * 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	public static function Init() {
		self::$db = new DB($config_db);
	}

	/**
	 * Scan and build a path map that route requests to the respective actions
	 */
	public static function Build() {
		$controllers = File::ScanDirectory(CONTROLLER_PATH, '/[a-z]+Controller\.php$/i');
		$paths = array();
		foreach ($controllers as $key => $object) {
			$paths = array_merge($paths, call_user_func($object->name . '::path'));
			
		}
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * @param string $uri
	 *  requested URI
	 *
	 * @return string
	 *  the corresponding controller class name
	 */
	public static function Dispatch($uri) {
		try {
			$sql = "SELECT controller, action FROM router WHERE = :uri";
			$record = self::$db->fetch($sql, $uri); 
			$controller = new $record['controller'];
			$action = call_user_func(array($controller, $record['action']));

			call_user_func(array($controller, 'beforeAction'), $params);
			call_user_func(array($controller, $action['callback']), $params);
			call_user_func(array($controller, 'afterAction'), $params);

		} catch (Exception $e) {
			echo $e->message();
		}
	}
}
