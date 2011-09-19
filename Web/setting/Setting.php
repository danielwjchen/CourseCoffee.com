<?php
/**
 * @file
 * Manage setting records in database
 */

interface SettingInterface {

	/**
	 * Define the database to populate
	 *
	 * @return array
	 */
	public function defineDB();

	/**
	 * Define the table to populate
	 *
	 * @return string
	 */
	public function defineTable();

	/**
	 * Define setting values
	 *
	 * @return string
	 */
	public function defineSetting();

	/**
	 * Populate settings as records in the database
	 */
	public function populateSetting() ;

	/**
	 * Populate settings as records in the database
	 *
	 * This is the factory method.
	 */
	public static function Populate() ;
}

abstract class Setting {

	/**
	 * Database connection(s)
	 */
	protected $db_array;

	function __construct() {
		global $config;
		$required_db = $this->defineDB();
		if (in_array('default', $required_db)) {
			$this->db_array['default'] = new DB($config->db['default']);
		}
		if (in_array('institution', $required_db)) {
			foreach ($config->db['institution'] as $db_name => $db_config) {
				$this->db_array[$db_name] = new DB($config->db['institution'][$db_name]);
			}
		}
	}

	function __destruct() {
		$this->db_array = null;
	}

	/**
	 * Get settings already stored in database
	 *
	 * @return array $records
	 */
	public function getSettingRecord() {
		$records = array();
		$table   = $this->defineTable();
		foreach ($this->db_array as $db) {
			$records = array_unique(array_merge(
				$records, 
				$db->fetchList("SELECT * FROM {$table}")
			));
		}

		return $records;
	}

	/**
	 * Populate setting records in database.
	 */
	public function populateSetting() {
		$table   = $this->defineTable();
		$setting = $this->defineSetting();

		foreach ($this->db_array as $db) {
			foreach ($setting as $item) {
				$record = $db->fetch(
					"SELECT * FROM {$table} WHERE {$item['field']} = :value",
					array('value' => $item['value'])
				);
				if (empty($record[$item['field']])) {
					$db->insert(
						"INSERT INTO {$table} ({$item['field']}) VALUE (:value)",
						array('value' => $item['value'])
					);
				}

			}
		}
	}

	/**
	 * Populate settings
	 *
	 * This factory method checks for existing value, and remove/skip
	 */
	public static function Populate() {
		$settings = File::ScanDirectory(SETTING_PATH, '/^[a-zA-Z]+Setting\.php$/');
		foreach ($settings as $path => $object) {
			require_once $path;
			try {
				$class = $object->name;
				$object = new $class();
				$object->populateSetting();
			} catch (Exception $e) {
				echo $e->Message();
			}
		}

	}
}
