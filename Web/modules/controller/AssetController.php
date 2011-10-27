<?php
/**
 * @file
 * Oversee acessses to javascript and css
 */
class AssetController {

	private $cache;

	function __construct() {
		//$this->cache = new FileCache();
		$this->cache = new DBCache();
	}

	/**
	 * Implement Controller::Route()
	 */
	public static function Route() {
		return array(
			'css' => 'getCSS',
			'js'  => 'getJS',
		);
	}

	/**
	 * Implement Controller::beforeAction()
	 *
	 *  this is a stub!
	 */
	public function beforeAction() {

	}

	/**
	 * Implement ControllerInterface::afterAction()
	 *
	 *  this is a stub!
	 */
	public function afterAction() {
	}

	public function getCSS($params = array()) {
		header('Content-type: text/css');
		global $config;
		$key = reset($params);
		
		if (!$config->compressCSS) {
			include ROOT_PATH . '/css/' . $key;
			return;
		}

		$key = str_replace('.css', '', $key);

		$value = $this->cache->get($key);
		echo $value;
	}

	public function getJS($params = array()) {
		header('Content-type: text/javascript');
		global $config;
		$key = reset($params);

		if (!$config->compressJS) {
			include ROOT_PATH . '/js/' . $key;
			return;
		
		}

		$key = str_replace('.js', '', $key);

		$value = $this->cache->get($key);
		echo $value;
	}
}
