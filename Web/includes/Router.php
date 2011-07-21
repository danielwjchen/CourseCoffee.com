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
	private static $db;

	/**
	 * Initialize the router
	 * 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	public static function Init($config_db) {
		self::$db = new DB($config_db);
	}

	/**
	 * Scan and build a path map that route requests to the respective actions
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public static function Build() {
		self::$db->perform('TRUNCATE TABLE router');
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
				self::$db->perform($sql, array(
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
	 *
	 * @return string
	 *  the corresponding controller class name
	 */
	public static function Dispatch($uri) {
		try {
			$sql = "SELECT controller, action FROM router WHERE path = :uri";
			$record = self::$db->fetch($sql, array('uri' => $uri)); 

			// if we can't resolve a controller action from the uri
			if (empty($record)) {
				$record['controller'] = 'PageController';
				$record['action'] = 'get404Page';
				Logger::Write(self::ERROR_404 . ' - ' . $uri, Logger::SEVERITY_LOW);
			}
			$controller = new $record['controller'];

			call_user_func(array($controller, 'beforeAction'));
			call_user_func(array($controller, $record['action']));
			call_user_func(array($controller, 'afterAction'));

		} catch (Exception $e) {
			Logger::Write($e->getMessage(), Logger::SEVERITY_LOW);
		}
	}
}
