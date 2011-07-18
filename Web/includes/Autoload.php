<?php
/**
 * @file
 * Resole file paths automatically
 */
class Autoload {

	/**
	 * A map of component paths and the regular expression that identifies an 
	 * implementation.
	 */
	private static $paths = array(
		DBA_PATH => '/DBA\.php$/',
		DAO_PATH => '/DAO\.php$/',
		MODEL_PATH => '/Model\.php$/',
		CONTROLLER_PATH => '/Controller\.php$/',
		VIEW_PATH => '/View\.php$/',
	);

	/**
	 * Manage database access
	 */
	private static $db;

	/**
	 * Initialize Autoload
	 *
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	public static function Init($config_db) {
		self::$db = new DB($config_db);
	}

	/**
	 * Scan and build a registry for the classes and their path.
	 */
	public static function Build() {
		$sql = "
			INSERT INTO autoload
				(class, path) 
			VALUES
				(:class, :path)
		";
		$classes = array();
		foreach (self::$paths as $path => $pattern) {
			$classes = array_merge($classes, FILE::ScanDirectory($path, $pattern));
		}

		foreach ($classes as $path => $file) {
			self::$db->perform(
				$sql, 
				array('class' => $file->name, 'path' => $file->uri)
			);
		}
	}

	/**
	 * Add the necessary file
	 */
	public static function Add($classname) {
		$path = self::$db->fetch(
			'SELECT path FROM autoload WHERE class = :class',
			array('class' => $classname)
		);

		require_once $path;
	}
}
