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
			'getCSS' => array(
				'css',
			),
			'getJS' => array(
				'js',
			),
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

	/**
	 * Get requested CSS
	 *
	 * I wish PHP supports method overloading.
	 *
	 * @param string $key
	 *  Define the module to this CSS request belongs to, or a key to compressed 
	 *  & cached CSS
	 * @param string $file
	 *  Define the CSS file to be included. This is also a flag to indicate if 
	 *  the request is asking for compressed & cached CSS
	 */
	public function getCSS($key, $file = '') {
		header('Content-type: text/css');
		global $config;
		
		if ($file != '' && !$config->compressCSS) {
			include MODULES_PATH . '/' . $key . '/css/' . $file;
			return;
		}

		$key = str_replace('.css', '', $key);

		$value = $this->cache->get($key);
		echo $value;
	}

	/**
	 * Get requested JS
	 *
	 * I wish PHP supports method overloading.
	 *
	 * @param string $key
	 *  Define the module to this JS request belongs to, or a key to compressed 
	 *  & cached JS
	 * @param string $file
	 *  Define the JS file to be included. This is also a flag to indicate if 
	 *  the request is asking for compressed & cached JS
	 */
	public function getJS($key, $file = '') {
		header('Content-type: text/javascript');
		global $config;

		if (!$config->compressJS) {
			include MODULES_PATH . '/' . $key . '/js/' . $file;
			return;
		}

		$key = str_replace('.js', '', $key);

		$value = $this->cache->get($key);
		echo $value;
	}
}
