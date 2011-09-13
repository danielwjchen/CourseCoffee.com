<?php
/**
 * @file
 * Resolve file paths automatically
 */
class Autoloader {

	/**
	 * @defgroup fail log 
	 * @{
	 */
	const FAIL_EMPTY_PATH = 'failed to resolve path';
	const WRONG_PATH      = 'path is pointed to a file that does not exist.';
	/**
	 * @}
	 */

	/**
	 * A map of component paths and the regular expression that identifies an 
	 * implementation.
	 */
	private static $paths = array(
		CACHE_PATH      => '/Cache\.php$/',
		SETTING_PATH    => '/Setting\.php$/',
		SCHEMA_PATH     => '/Schema\.php$/',
		DAO_PATH        => '/DAO\.php$/',
		MODEL_PATH      => '/Model\.php$/',
		CONTROLLER_PATH => '/Controller\.php$/',
		VIEW_PATH       => '/View\.php$/',
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
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public static function Build() {
		self::$db->perform('TRUNCATE TABLE `autoloader`');
		$sql = "
			INSERT INTO `autoloader`
				(`class`, `path`) 
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
				array('class' => $file->name, 'path' => $path)
			);
		}
	}

	/**
	 * Add the necessary file
	 */
	public static function Add($classname) {
		$record = self::$db->fetch(
			'SELECT `path` FROM `autoloader` WHERE `class` = :class',
			array('class' => $classname)
		);

		if (empty($record['path'])) {
			Logger::write(self::FAIL_EMPTY_PATH . ' - ' . $classname, Logger::SEVERITY_HIGH);
			header('Location: /all-system-down');
		}

		try {
			require_once $record['path'];

		} catch (Exception $e) {
			Logger::write(self::WRONG_PATH . ' - ' . $classname, Logger::SEVERITY_HIGH);
			header('Location: /all-system-down');
		}
	}
}
