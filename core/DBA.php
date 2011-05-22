<?php

/**
 * Define the database schema to be created in a DBA request
 */
interface DBATemplate{
	/**
	 * Create the definition of a database schema
	 *
	 * @return array $definition
	 *  an associative array that uses keys and values to define a table
	 *  - 
	 */
	public function schema();
}

/**
 * Handle database administrative tasks.
 */
class DBAFactory{
	
	/**
	 * Create table from schema
	 *
	 * @param string $dba_request
	 *  name of the DBA request
	 *  An associative array that mirrors the table sctructure
	 *
	 * @return bool $result
	 */
	static function Install($dba_request) {
		require_once('../dba/'.$dba_request.'.php');

		$dba_class = $dba_request.'_DBA';
		$dba_schema = call_user_func($dba_request.'_DBA::schema');

		$dba_sql = 'CREATE TABLE '.$dba_schema['name'].'(';

		foreach ($dba_schema['column'] as $column_name => $column) {
			$dba_sql .= $column_name . ' ' . $column['type'];
			$dba_sql .= isset($column['size']) ? '(' .$column['size'] ."), " : ", ";
		}
		$dba_sql .= ')';

		echo $dba_sql;
	}

	/**
	 * Update table from schema
	 *
	 * It checks with existing table schema to generate the update statement
	 *
	 * @param array $schema
	 *  An associative array that mirrors the table sctructure
	 *
	 * @return bool $result
	 */
	static function Update($schema) {
	}

	/**
	 * Remove table from schema
	 *
	 * @param array $schema
	 *  An associative array that mirrors the table sctructure
	 *
	 * @return bool $result
	 */
	static function Remove($schema) {
	}
}
