<?php
/**
 * @file
 * Oversee acessses to javascript and css
 */
class AssetController extends Controller implements ControllerInterface {
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

		$db_cache = new DBCache();
		$value = $db_cache->get($key);
		echo $value;
	}
}
