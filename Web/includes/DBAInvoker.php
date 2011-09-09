<?php
/**
 * @file
 * Perform database administrative tasks
 *
 * This is heavily influenced by drupal 6.
 */ 

require_once DBA_PATH . '/DBAInterface.php';

/**
 * Handle database administrative tasks.
 */
class DBAInvoker{

	/**
	 * an associative array of PDO database connection
	 */
	static private $db;

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
				$key_array[] = $key[0] . '(`' . $key[1] . '`)';

			} else {
				$key_array[] = '`' . $key . '`';
			}
		}

		return implode(', ', $key_array);
	}

	/**
	 * Create the SQL query for the key columns
	 *
	 * @param object $db
	 *  A database connection object.
	 * @param array $table_new
	 *  An associative array that mirrors the table sctructure
	 * @param array $table_old
	 *  An associative array that mirrors the table sctructure
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return array $dba_sql
	 *  an array of key clause SQL queries
	 */
	static private function alter_key_sql($table_new, $table_old) {
		$dba_sql  = array();
		$add_sql  = array();
		$drop_sql = array();
		$key_type = array('primary', 'unique', 'index');

		foreach ($key_type as $type) {
			$sql_key = strtoupper($type);
			$sql_key = $sql_key == 'PRIMARY' ? $sql_key .' KEY ' : $sql_key;

			$request_key = isset($table_new[$type]) ? array_keys($table_new[$type]) : array();
			$exist_key   = isset($table_old[$type]) ? array_keys($table_old[$type]) : array();
			$same_key    = array_intersect($request_key, $exist_key);
			$drop_key = array_diff($exist_key, $same_key);
			$add_key  = array_diff($request_key, $same_key);


			if (!empty($drop_key)) {

				if ($type == 'primary' ) {
					$dba_sql[] = ' DROP ' . $sql_key . ' `' . $drop_key . '` ';
				} else {
					$dba_sql[] = ' DROP ' . $sql_key .' ' . implode(', ', $drop_key) . '';
				}
			}

			if (!empty($add_key)) {
				if ($type == 'primary' ) {
					$dba_sql[] = ' ADD ' . $sql_key . ' `' . $add_key . '` ';
				} else {
					foreach ($add_key as $key_name) {
						$dba_sql[] = 'ADD ' . $sql_key .' `' . $key_name . '` (' . implode(', ', $table_new[$type][$key_name]) . ')';
					}
				}
			}
		}
		return $dba_sql;
	}


	/**
	 * Rebuild DBA schema array from actual table schema
	 *
	 * This is not finished...
	 *
	 * @param string $table_name
	 *
	static private function BuildSchemaFromDB($table_name) {
		$table_def = self::$db->fetchList('DESCRIBE `' . $table_name .'`');
		$unique  = array();
		$index   = array();
		$primary = array();
		$column  = array();

		$type = '';
		$size = '';
		foreach ($table_def as $i => $col) {
			if ($col['Extra']) {
				$type = 'serial';
			} elseif (strpos($col['Type'], 'int(11)')) {
				$type = 'int';
			} elseif (strpos($col['Type'], 'char')) {
				$type = 'int';
				preg_match('/[0-9]{0,5}/', $col['Type'], $matches);
				$size = reset($matches);
			} 

			$column[$col['Field']] = array(
				'type' => $type,
			);
		}

	}
	*/

	/**
	 * Initialize the DBAInvoker
	 *
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	static function Init(array $config_db) {
		self::$db = new DB($config_db);
	}

	/**
	 * Process a DBA Request.
	 *
	 * @param string $dba_request
	 *  name of the DBA request
	 * @param string $dba_path
	 *  a string to specify the DBA file path 
	 *
	 * @return bool $result
	 */
	static function Request($dba_request, $dba_path) {
		require_once($dba_path);

		$dba_schema = call_user_func($dba_request . '::schema');
		$dba_record  = self::$db->fetch(
			'SELECT * FROM DBA WHERE request = :request',
			array('request' => $dba_request)
		);

		$encoded_schema = json_encode($dba_schema);

		$sql = '';
		if (empty($dba_record['schema'])) {
			self::Create($dba_schema);
			$sql = '
				INSERT INTO DBA (`request`, `schema`, `timestamp`)
				VALUES (:request, :schema, UNIX_TIMESTAMP())
			';
			
		} else {
			self::Alter($dba_schema, json_decode($dba_record['schema'], true));
			$sql = '
				UPDATE DBA SET
					`schema` = :schema,
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = :request
			';
		}
		self::$db->perform($sql, array(
			'schema'  => $encoded_schema,
			'request' => $dba_request,
		));

		$encoded_script = '';
		if (method_exists($dba_request, 'script')) {
			$dba_sql = call_user_func($dba_request . '::script');
			$encoded_script = json_encode($dba_sql);
			if (empty($dba_record['script']) || $dba_record['script'] !== $encoded_script) {
				foreach($dba_sql as $sql) {
					self::Perform($sql);
				}
			} else {
				$encoded_script = $dba_record['script'];
			}
			self::$db->perform("
				UPDATE DBA SET
					`script` = :script,
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = :request
				",
				array(
					'script'  => $encoded_script,
					'request' => $dba_request,
				)
			);
		}

	}

	/**
	 * Perform SQL script
	 *
	 * @param string $dba_sql
	 */
	public static function Perform($dba_sql) {
		self::$db->perform($dba_sql);
	}

	/**
	 * Create table
	 *
	 * This is a helper method to share code among Create() and Alter()
	 *
	 * @param string $name
	 *  name of the table
	 * @param array $table
	 *  An associative array that mirrors the table sctructure
	 */
	private static function CreateTable($name, $table) {
		$dba_sql = 'CREATE TABLE ' . $name . '(';

		// compose columns
		$columns = array();
		foreach ($table['column'] as $column_name => $column) {
			$columns[] = self::create_column_sql($column_name, self::process_column($column));
		}
		$dba_sql .= implode(", \n", $columns) . ", \n";

		$keys = array();

		// compose primary key
		if (isset($table['primary'])) {
			$keys[] = 'PRIMARY KEY (' . self::create_key_sql($table['primary']) . ') ';
		}

		// compose unique keys
		if (isset($table['unique'])) {
			foreach ($table['unique'] as $unique => $column) {
				$keys[] = 'UNIQUE KEY ' . $unique . ' (' . self::create_key_sql($column) . ') ';
			}
		}

		// compose indexes
		if (isset($table['index'])) {
			foreach ($table['index'] as $index => $column ) {
				$keys[] = 'INDEX ' . $index . ' (' . self::create_key_sql($column) . ') ';
			}
		}
		
		$dba_sql .= implode(', ', $keys) .')';

		self::$db->perform($dba_sql);
	}

	/**
	 * Create table from schema
	 *
	 * @param array $dba_schema
	 *  An array of table schemas that mirrors the table sctructure
	 */
	public static function Create($dba_schema) {

		foreach ($dba_schema as $name => $table) {
			self::CreateTable($name, $table);

		}

	}

	/**
	 * Alter a table from schema
	 *
	 * This method compares the new schema with the old (existing) one and computes
	 * a series of queries the modify the table. It doesn't handle DROP operation.
	 *
	 * @param array $schema_new
	 *  An associative array that mirrors the table sctructure
	 * @param array $schema_old
	 *  An associative array that mirrors the table sctructure
	 * @param string $dba_path
	 *  an optional string to specify the DBA file path if different from default
	 *
	 * @return bool $result
	 */
	static function Alter($schema_new, $schema_old) {

		$update_tables  = array();
		$drop_tables    = array();
		$add_tables     = array();

		$exist_tables  = array_keys($schema_old);
		$exist_tables  = is_array($exist_tables) ? $exist_tables : array();
		$request_tables = array_keys($schema_new); 
		$request_tables = is_array($request_tables) ? $request_tables : array();
		$update_tables  = array_intersect($exist_tables, $request_tables);
		$add_tables = array_diff($request_tables, $update_tables);
		if (!empty($add_tables)) {
			foreach ($add_tables as $table_name) {
				self::CreateTable($table_name, $schema_new[$table_name]);
			}
		}

		$drop_tables = array_diff($exist_tables, $update_tables);
		if (!empty($drop_tables)) {
			foreach ($drop_tables as $table_name) {
				self::$db->perform('DROP TABLE `' . $table_name . '`');
			}
		}
		if (!empty($update_tables)) {

			foreach ($update_tables as $table_name) {
				$request_cols = array_keys($schema_new[$table_name]['column']);
				$exist_cols   = array_keys($schema_old[$table_name]['column']);
				$update_cols  = array_intersect($request_cols, $exist_cols);
				$drop_cols    = array_diff($exist_cols, $update_cols);
				$add_cols     = array_diff($request_cols, $update_cols);

				$dba_sql = array();

				foreach ($drop_cols as $col_name) {
					$dba_sql[] = 'DROP ' . $col_name . "\n";
				}

				foreach ($add_cols as $col_name) {
					$dba_sql[] = '
						ADD ' . 
						self::create_column_sql($col_name, self::process_column($schema_new[$table_name]['column'][$col_name])) .
					"\n";
				}

				foreach ($update_cols as $col_name) {
					$dba_sql[] = '
						CHANGE `' .$col_name . '` ' .
						self::create_column_sql($col_name, self::process_column($schema_new[$table_name]['column'][$col_name])) .
					"\n";
				}

				$dba_sql = array_merge($dba_sql, self::alter_key_sql($schema_new[$table_name], $schema_old[$table_name]));

				$sql = 'ALTER TABLE `' . $table_name . '` ' . implode(', ', $dba_sql);

				self::$db->perform($sql);
			}
		}

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
