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
	 * Implement Controller::path()
	 */
	public static function path() {
		return array(
			'css' => 'getCSS',
			'js'  => 'getJS',
		);
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
