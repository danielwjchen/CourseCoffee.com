<?php
/**
 * @file
 * Route requests to their responding controller class
 */

class Router {

	/**
	 * An associative array that maps URI to a controller class
	 */
	private static $map;

	/**
	 * Access to router records in database
	 */
	private static $dao;

	/**
	 * Initialize the router
	 * 
	 * This functions loads all router files into a map
	 */
	public static function Init() {
		self::$map = array();
		self::$dao = Factory::DAO('Router');
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
	 * Scan and build a path map that route requests to the respective 
	 * actions
	 *
	 */
	public static function Build() {
	
	}

	/**
	 * Convert a given URI to the responding controller class name
	 *
	 * @param string $uri_base
	 *  requested URI
	 * @param string $uri_action
	 *  requested URI
	 *
	 * @return string
	 *  the corresponding controller class name
	 */
	public static function Dispatch($uri_base, $uri_action) {
		try {
			self::$dao->read($uri_base); 
			$controller = Factory::Controller(self::$dao->controller);
			$action = call_user_func(array($controller, 'path'), $uri_action);
			$params = array();
			switch ($action['params']) {
				case 'post':
					$params = Input::POST($action['post']);
				case 'get':
					$params = array_merge($params, Input::GET($action['get']));
					break;
			}

			call_user_func(array($controller, 'beforeAction'), $params);
			call_user_func(array($controller, $action['callback']), $params);
			call_user_func(array($controller, 'afterAction'), $params);

		} catch (Exception $e) {
			echo $e->message();
		}
	}
}
