<?php

require_once TEST_CORE_DAO_PATH . '/DAOSetup.php';

abstract class LinkageDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Define the table attributes
	 *
	 * - parent
	 * - child
	 * - table
	 */
	protected static $table_attribute;

	/**
	 * Get the DAO class name from table
	 *
	 * @param string $table_name
	 *  name of the table in database
	 *
	 * @return string
	 *  name of the DAO class
	 */
	protected static function getDAOClass($table_name) {
		$table_element = explode('_', $table_name);

		foreach ($table_element as $key => $value) {
			$table_element[$key] = ucfirst($value);

		}

		return implode('', $table_element);
	}

	/**
	 * Extend DAOSetup::Prepare()
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$new_params = array();
		$called_class = get_called_class();

		// get linkage table name
		$linkage_table = $called_class::$table_attribute['table'];

		// get tables
		$parent_table = $called_class::$table_attribute['parent'];
		$child_table = $called_class::$table_attribute['child'];

		// get primary keys
		$parent_id = $parent_table . '_id';
		$child_id = $child_table . '_id';


		if (isset($params['specified'])) {

			$new_params = array(
				$parent_id => $params['specified']['parent_id'],
				$child_id => $params['specified']['child_id'],
			);
		} else {
			// get DAOSetup class
			$parent_class = $called_class::getDAOClass($called_class::$table_attribute['parent']) . 'DAOSetup';
			$child_class = $called_class::getDAOClass($called_class::$table_attribute['child']) . 'DAOSetup';
			require_once TEST_CORE_DAO_PATH . '/setups/' . $parent_class . '.php';
			require_once TEST_CORE_DAO_PATH . '/setups/' . $child_class . '.php';
			$parent = $parent_class::CleanUp();
			$child = $child_class::CleanUp();
			$parent = $parent_class::Prepare($params);
			$child = $child_class::Prepare($params);

			$new_params = array(
				$parent_id => $parent['record']['id'],
				$child_id => $child['record']['id']
			);

		} 

		self::$db->perform("
			INSERT INTO `{$linkage_table}` ({$parent_id}, {$child_id}) 
				VALUES (:{$parent_id}, :{$child_id})",
			$new_params
		);

		$record = self::$db->fetch("
			SELECT * FROM `{$linkage_table}` 
			WHERE {$parent_id} = :{$parent_id} 
			AND {$child_id} = :{$child_id}", 
			$new_params
		);

		return array('record' => $record, 'params' => $new_params);
	}

	/**
	 * Extend DAOSetup::CleanUp()
	 */
	public static function CleanUp() {
		parent::CleanUp();
		$called_class = get_called_class();
		$parent_class = $called_class::getDAOClass($called_class::$table_attribute['parent']) . 'DAOSetup';
		$child_class = $called_class::getDAOClass($called_class::$table_attribute['child']) . 'DAOSetup';
		require_once TEST_CORE_DAO_PATH . '/setups/' . $parent_class . '.php';
		require_once TEST_CORE_DAO_PATH . '/setups/' . $child_class . '.php';
		$parent_class::CleanUp();
		$child_class::CleanUp();
	}

}
