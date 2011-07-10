<?php
/**
 * @file
 * Route requests to their responding controller class
 */

class RouterInvoker {

	/**
	 * An associative array that maps URI to a controller class
	 */
	private static $map;

	/**
	 * Initialize the router
	 * 
	 * This functions loads all router files into a map
	 */
	public static function Init() {
		self::$map = array();
		$routers = File::ScanDirectory(ROUTER_PATH, '/Router\.php$/');
		foreach ($routers as $path => $router) {
			try {
				require_once $router->uri;
				self::$map = array_merge(self::$map, call_user_func($router->name . '::Define'));
			} catch (Exception $e) {
				echo $e->Message();
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
		if (isset(self::$map[$uri])) {
			return self::$map[$uri];
		} else {
			throw new Exception('unknown URI');
		}
	}
}
