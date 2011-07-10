<?php
/**
 * @file
 * The DBA system.
 *
 * This system is heavily influenced by drupal 6.
 */ 

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
class DBA{

	/**
	 * This maps a generic data type in combination with its data size
	 * to the engine-specific data type.
	 *
   * Put :normal last so it gets preserved by array_flip.  This makes
   * it much easier for modules (such as schema.module) to map
   * database types back into schema types.
	 */
	static private function type_map($type) {
		$map = array(
			'varchar:normal'  => 'VARCHAR',
			'char:normal'     => 'CHAR',

			'text:tiny'       => 'TINYTEXT',
			'text:small'      => 'TINYTEXT',
			'text:medium'     => 'MEDIUMTEXT',
			'text:big'        => 'LONGTEXT',
			'text:normal'     => 'TEXT',

			'serial:tiny'     => 'TINYINT',
			'serial:small'    => 'SMALLINT',
			'serial:medium'   => 'MEDIUMINT',
			'serial:big'      => 'BIGINT',
			'serial:normal'   => 'INT',

			'int:tiny'        => 'TINYINT',
			'int:small'       => 'SMALLINT',
			'int:medium'      => 'MEDIUMINT',
			'int:big'         => 'BIGINT',
			'int:normal'      => 'INT',

			'float:tiny'      => 'FLOAT',
			'float:small'     => 'FLOAT',
			'float:medium'    => 'FLOAT',
			'float:big'       => 'DOUBLE',
			'float:normal'    => 'FLOAT',

			'numeric:normal'  => 'DECIMAL',

			'blob:big'        => 'LONGBLOB',
			'blob:normal'     => 'BLOB',

			'datetime:normal' => 'DATETIME',
		);

		return $map[$type];
	}

	/**
	 * Set database-engine specific properties for a field.
	 *
	 * @param $field
	 *   A field description array, as specified in the schema documentation.
	 */
	static private function process_column($column) {

		if (!isset($column['size'])) {
			$column['size'] = 'normal';
		}

		// Set the correct database-engine specific datatype.
		if (!isset($column['mysql_type'])) {
			$column['mysql_type'] = self::type_map($column['type'] .':'. $column['size']);
		}

		if ($column['type'] == 'serial') {
			$column['auto_increment'] = TRUE;
		}

		return $column;
	}

	/**
	 * Create an SQL string for a column to be used in table creation or alteration.
	 *
	 * Before passing a field out of a schema definition into this function it has
	 * to be processed by _db_process_field().
	 *
	 * @param $name
	 *    Name of the field.
	 * @param $spec
	 *    The field specification, as per the schema data structure format.
	 */
	static private function create_column_sql($name, $spec) {
		$sql = "`". $name ."` ". $spec['mysql_type'];

		if (in_array($spec['type'], array('varchar', 'char', 'text')) && isset($spec['length'])) {
			$sql .= '('. $spec['length'] .')';
		}
		elseif (isset($spec['precision']) && isset($spec['scale'])) {
			$sql .= '('. $spec['precision'] .', '. $spec['scale'] .')';
		}

		if (!empty($spec['unsigned'])) {
			$sql .= ' unsigned';
		}

		if (!empty($spec['not null'])) {
			$sql .= ' NOT NULL';
		}

		if (!empty($spec['auto_increment'])) {
			$sql .= ' auto_increment';
		}

		if (isset($spec['default'])) {
			if (is_string($spec['default'])) {
				$spec['default'] = "'". $spec['default'] ."'";
			}
			$sql .= ' DEFAULT '. $spec['default'];
		}

		if (empty($spec['not null']) && !isset($spec['default'])) {
			$sql .= ' DEFAULT NULL';
		}

		return $sql;
	}

	/**
	 * Create the key columns in an SQL query.
	 */
	static private function create_key_sql($keys) {
		$key_array = array();
		foreach ($keys as $key) {
			if (is_array($key)) {
				$key_array[] = $key[0] . '(' . $key[1] . ')';

			} else {
				$key_array[] = $key;
			}
		}

		return implode(', ', $key_array);
	}

	/**
	 * Create the SQL query for the key columns
	 *
	 * @param string $type
	 *  type of key, i.e primary, unique, index.
	 * @param object $db
	 *  A database connection object.
	 * @param array $schema_new
	 *  An associative array that mirrors the table sctructure
	 * @param array $schema_old
	 *  An associative array that mirrors the table sctructure
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return array $dba_sql
	 *  an array of key clause SQL queries
	 */
	static private function alter_key($type, $schema_new, $schema_old) {
		$dba_sql = array();
		$add_sql = array();
		$drop_sql = array();
		$sql_key = strtoupper($type);
		$sql_key = $sql_key == 'PRIMARY' ? $sql_key .' KEY' : $sql_key;

		if (isset($schema_new[$type])) {

			if (!isset($schema_old[$type])) {
				$add_sql[] = self::create_key_sql($schema_new[$type]);

			} elseif ($schema_new[$type] != $schema_old[$type]) {

				$keys = array_unique(array_merge($schema_old[$type], $schema_new[$type]));

				foreach ($keys as $key) {

					if (!in_array($key, $schema_old[$type])) {
						$add_sql[] = $key;

					} elseif (!in_array($key, $schema_new[$type])) {
						$drop_sql[] = $key;

					}
				}
			}
		} else {
			$drop_sql[] = self::create_key_sql($schema_old[$type]);
		}

		if (!empty($drop_sql)) {

			if ($type == 'primary' ) {
				$dba_sql[] = 'DROP ' . $sql_key;
			} else {
				$dba_sql[] = 'DROP ' . $sql_key .' (' . implode(', ', $drop_sql) . ')';
			}
		}

		if (!empty($add_sql)) {
			$dba_sql[] = 'ADD ' . $sql_key .' (' . implode(', ', $add_sql) . ')';
		}

		return $dba_sql;
	}

	/**
	 * Create a DBA class from the request
	 *
	 * @param string $request
	 *  name of the DBA request
	 *
	 * @return string
	 *  a DBA class
	 */
	static private function checkRequestClass($request) {
		$request_array = array();
		$request_word = explode('_', $request);

		foreach ($request_word as $word) {
			$request_array[] = ucfirst($word);
		}

		return implode('', $request_array) . 'DBA';
	}

	/**
	 * Process a DBA Request.
	 *
	 * @param object $db
	 *  A database connection object.
	 * @param string $dba_request
	 *  name of the DBA request
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return bool $result
	 */
	static function Request($db, $dba_request, $dba_path = NULL) {
		if (empty($dba_path)) {
			require_once(DBA_PATH. $dba_request.'.php');
		} else {
			require_once($dba_path);
		}

		$dba = self::checkRequestClass($dba_request);

		$dba_schema = call_user_func($dba . '::schema');
		$dba_record  = $db->fetch(
			"SELECT `schema` FROM `DBA` 
				WHERE `request` = '" . $dba_request . "'"
		);

		$sql = '';
		if (!isset($dba_record['schema'])) {
			self::Create($db, $dba_schema);
			$sql = "
				INSERT INTO `DBA` (`request`, `schema`, `timestamp`)
				VALUES ('".$dba_request."', '".json_encode($dba_schema)."', UNIX_TIMESTAMP())
			";
			
		} elseif (json_decode($dba_record['schema'], true) != $dba_schema) {
			self::Alter($db, $dba_schema, $dba_record);
			$sql = "
				UPDATE `DBA` SET
					`schema` = '".json_encode($dba_schema)."',
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = '" . $dba_request . "'
			";
		}

		$db->perform($sql);
	}

	/**
	 * Create table from schema
	 *
	 * @param object $db
	 *  A database connection object.
	 * @param array $dba_schema
	 *  An associative array that mirrors the table sctructure
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return bool $result
	 */
	static function Create($db, $dba_schema) {

		$dba_sql = 'CREATE TABLE '.$dba_schema['name'].'(';

		$columns = array();
		foreach ($dba_schema['column'] as $column_name => $column) {
			$dba_sql .= self::create_column_sql($column_name, self::process_column($column)) .", \n";
		}
		$dba_sql .= implode(', ', $columns);

		$keys = array();

		if (isset($dba_schema['primary'])) {
			$keys[] = 'PRIMARY KEY (' . self::create_key_sql($dba_schema['primary']) . ') ';
		}

		if (isset($dba_schema['unique'])) {
			foreach ($dba_schema['unique'] as $unique => $column) {
				$keys[] = 'UNIQUE KEY ' . $unique . ' (' . self::create_key_sql($column) . ') ';
			}
		}

		if (isset($dba_schema['index'])) {
			foreach ($dba_schema['index'] as $index => $column ) {
				$keys[] = 'INDEX ' . $index . ' (' . self::create_key_sql($column) . ') ';
			}
		}
		
		$dba_sql .= implode(', ', $keys) .')';

		$db->perform($dba_sql);
	}

	/**
	 * Alter a table from schema
	 *
	 * This method compares the new schema with the old (existing) one and computes
	 * a series of queries the modify the table. It doesn't handle DROP operation.
	 *
	 * @param object $db
	 *  A database connection object.
	 * @param array $schema_new
	 *  An associative array that mirrors the table sctructure
	 * @param array $schema_old
	 *  An associative array that mirrors the table sctructure
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return bool $result
	 */
	static function Alter($db, $schema_new, $schema_old) {

		$table = $schema_new['name'];
		$dba_sql = array();

		$columns = array();
		foreach ($schema_new['column'] as $column_name => $column) {

			if (!isset($schema_old['column'][$column_name])) {
				$dba_sql[] = '
					ADD ' . self::create_column_sql($column_name, self::process_column($column)) .
				"\n";

			} elseif ($schema_new['column'][$column_name] != $schema_old['column'][$column_name]) {
				$dba_sql[] = '
					CHANGE `' .$column_name . '` ' .
					self::create_column_sql($column_name, self::process_column($column)) .
				"\n";
			}
		}

		$dba_sql = array_merge($dba_sql, self::alter_key('primary', $schema_new, $schema_old));
		$dba_sql = array_merge($dba_sql,self::alter_key('unique', $schema_new, $schema_old));
		$dba_sql = array_merge($dba_sql,self::alter_key('index', $schema_new, $schema_old));


		$sql = 'ALTER TABLE `' . $schema_new['name'] . '` ' .	implode(', ', $dba_sql);

		return $db->perform($sql);
	}

	/**
	 * Remove table from database 
	 *
	 * @param object $db
	 *  A database connection object.
	 * @param string $request
	 *  the table requested to be dropped
	 *
	 * @return bool $result
	 */
	static function Remove($db, $request) {
		$drop_sql = 'DROP TABLE `'.$request.'`';
		$db->perform($drop_sql);

		$delete_sql = "DELETE FROM `DBA` WHERE `request` = '{$request}'";
		$db->perform($delete_sql);
	}
}
