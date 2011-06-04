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
	 *  - name
	 *  - columns
	 *  - key (optional)
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

		$columns = array();
		foreach ($dba_schema['column'] as $column_name => $column) {
			$column_sql = $column_name . ' ' . $column['type'];
			$column_sql.= isset($column['size']) ? '(' .$column['size'] .')' : '';
			$columns[] = $column_sql;
		}
		$dba_sql .= implode(', ', $columns);

		if (isset($dba_schema['key'])) {
			$keys = array();
			foreach ($dba_schema['key'] as $key) {
				if (isset($key['primary'])) {
					$keys[] = 'PRIMARY KEY (' . $this->create_key_sql($key['primary']) . ') ';
				}
				if (isset($key['unique'])) {
					foreach ($key['unique'] as $key_name => $key_def ) {
						$keys[] = 'UNIQUE KEY ' . $key_name . ' (' . $this->create_key_sql($key_def) . ') ';
					}
				}
				if (isset($key['index'])) {
					foreach ($key['index'] as $key_name => $key_def ) {
						$keys[] = 'INDEX ' . $key_name . ' (' . $this->create_key_sql($key_def) . ') ';
					}
				}
			}
			$dba_sql .= implode(', ', $keys);
		}


		$dba_sql .= ')';
		echo $dba_sql;
	}

	private function create_key_sql($keys) {
		print_r($keys);
		foreach ($keys as $key) {
			$sql .= ' ' . $key . '';
		}

		return $sql;
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
