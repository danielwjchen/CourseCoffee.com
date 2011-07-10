<?php

class Utility {

	/**
	 * Get the table name from DAO class
	 *
	 * @param string $class_name
	 *  name of the DAO class
	 * @param string $
	 *
	 * @return string $table_name
	 * name of the table in database
	 */
	public static function GetTableName($class_name) {
		$table_name = strtolower(
			preg_replace('/([a-z0-9])([A-Z])/','$1_$2', 
			str_replace('TypeDAOSetup', '', $class_name))
		);

		return $table_name;
	}
}
