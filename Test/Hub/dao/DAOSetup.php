<?php

require_once TEST_CORE_DAO_PATH . '/DAOSetupInterface.php';
require_once TEST_CORE_PATH . '/HubSetup.php';
		
/**
 * Base class for all dao setups
 */
abstract class DAOSetup extends HubSetup implements DAOSetupInterface{

	/**
	 * The corresponding table of the DAO class
	 */
	protected static $table_name;

	/**
	 * Extend HubSetup::Init
	 */
	public static function Init($db_config) {
		parent::Init($db_config);
		self::$table_name = self::getTableName(__CLASS__);
	}

	/**
	 * Get the table name in order to manipulate data
	 *
	 * @param string $class_name
	 *  name of the class
	 * @param string $tail
	 *  segment of the string to be removed, default to DAOSetup
	 * 
	 * @return string $table_name
	 *  name of the corresponding table
	 */
	protected static function getTableName($class_name, $tail = 'DAOSetup') {
		$table_name = strtolower(
			preg_replace('/([a-z0-9])([A-Z])/','$1_$2', 
			str_replace($tail, '', $class_name))
		);

		return $table_name;
	}

	/**
	 * Truncate table in database
	 *
	 * @param string $table
	 *  name of the table
	 */
	protected static function truncateTable($table) {
		self::$db->perform("TRUNCATE TABLE `{$table}`");
	}

	/**
	 * Implement DAOSetupInterface::Prepare()
	 */
  public static function Prepare(array $params = array('random' => true)) {
		$called_class = get_called_class();
		$called_class::$table_name = $called_class::getTableName(get_called_class());
		if (!isset($params['no_truncate'])) {
			$called_class::truncateTable($called_class::$table_name);

		}

  }

	/**
	 * Implement DAOSetupInterface::Prepare()
	 */
  public static function CleanUp() {
		$called_class = get_called_class();
		$called_class::truncateTable($called_class::$table_name);
  }
}
