<?php
/**
 * @file
 * Resolve file paths automatically
 *
 * This class implements a singleton design. Be careful with that.
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

	const MODULE_PATTERN = '/(Cache|Controller|DAO|Model|Schema|Setting|View)\.php$/';

	/**
	 * Singleton instance
	 */
	public static $instance;

	/**
	 * A map of component paths and the regular expression that identifies an 
	 * implementation.
	 */
	private $paths;

	/**
	 * Database connection
	 */
	private $db;

	/**
	 * Default constructor 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	function __construct($config_db) {
		$this->db = new DB($config_db);
		$this->paths = array(
			INCLUDES_PATH . '/modules' => self::MODULE_PATTERN,
			MODULES_PATH => self::MODULE_PATTERN,
			CACHE_PATH => '/Cache\.php$/',
		);

	}

	function __destruct() {
		$this->db = null;
	}

	/**
	 * Initialize an Autoloader instance
	 *
	 * This checks if an instance of this class already exists
	 */
	private static function Init() {
		global $config;
		if (self::$instance == null) {
			self::$instance = new static($config->db['default']);
		}
	}

	/**
	 * Scan and build a registry for the classes and their path.
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public function buildPath() {
		$this->db->perform('TRUNCATE TABLE `autoloader`');
		$sql = "
			INSERT INTO `autoloader`
				(`class`, `path`) 
			VALUES
				(:class, :path)
		";
		$classes = array();
		foreach ($this->paths as $path => $pattern) {
			$classes = array_merge($classes, File::Scan($path, $pattern));
		}
		foreach ($classes as $path => $file) {
			$this->db->perform(
				$sql, 
				array('class' => $file->name, 'path' => $path)
			);
		}
	}

	/** 
	 * Resolve file paths and include necessary file
	 *
	 * @param string $classname
	 */
	public function resolvePath($classname) {
		$record = $this->db->fetch(
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

	/**
	 * Scan and build a registry for the classes and their path.
	 *
	 * This is a factory method that creates a singleton instance
	 *
	 * WARNING!! This method clears the table and repopulates it with new data.
	 */
	public static function Build() {
		self::Init();
		self::$instance->buildPath();
	}

	/**
	 * Resolve file paths and include necessary file
	 *
	 * This is a factory method that creates a singleton instance
	 *
	 * @param string $classname
	 */
	public static function Resolve($classname) {
		self::Init();
		self::$instance->resolvePath($classname);
	}
}
