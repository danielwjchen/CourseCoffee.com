<?php
/**
 * @file
 * Oversee acessses to javascript and css assets
 *
 * @to-do
 * This is implemented rather hastly and slappily. I shall come back and fix it.
 */
class AssetController {

	private $cache;

	function __construct() {
		//$this->cache = new FileCache();
		$this->cache = new DBCache();
	}

	/**
	 * Implement Controller::path()
	 */
	public static function Route() {
		return array(
			'css' => 'getCSS',
			'js' => 'getJS',
		);
	}

	/**
	 * Implement ControllerInterface::action()
	 */
	public function beforeAction() {
	}
	public function afterAction() {
	}

	/**
	 * Get requested CSS
	 *
	 * I wish PHP supports method overloading.
	 *
	 * @param string $key
	 *  Define the module to this CSS request belongs to, or a key to
compressed 
	 *  & cached CSS
	 * @param string $file
	 *  Define the CSS file to be included. This is also a flag to indicate
if 
	 *  the request is asking for compressed & cached CSS
	 */
	public function getCSS($params) {
		header('Content-type: text/css');
		global $config;

		if (!$config->compressCSS) {
			include MODULES_PATH . '/' . $params[0]. '/css/' . $params[1];
			return;
		}

		$key = str_replace('.css', '', $params[0]);

		$value = $this->cache->get($key);
		echo $value;
	}

	/**
	 * Get requested JS
	 *
	 * I wish PHP supports method overloading.
	 *
	 * @param string $key
	 *  Define the module to this JS request belongs to, or a key to
compressed 
	 *  & cached JS
	 * @param string $file
	 *  Define the JS file to be included. This is also a flag to indicate
if 
	 *  the request is asking for compressed & cached JS
	 */
	public function getJS($params) {
		header('Content-type: text/javascript');
		global $config;

		if (!$config->compressJS) {
			include MODULES_PATH . '/' . $params[0]. '/js/' . $params[1];
			return;
		}

		$key = str_replace('.js', '', $params[0]);

		$value = $this->cache->get($key);
		echo $value;
	}
}
