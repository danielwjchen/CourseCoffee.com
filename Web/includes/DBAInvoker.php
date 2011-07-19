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
	 * Initialize the DBAInvoker
	 *
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	static function Init(array $config_db) {
		self::$db['sys'] = new DB($config_db['sys']);
		self::$db['core'] = new DB($config_db['core']);
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
		$dba_record  = self::$db['sys']->fetch(
			'SELECT `schema` FROM DBA
				WHERE `request` = :request',
			array('request' => $dba_request)
		);

		$dba_encoded = json_encode($dba_schema);

		$sql = '';
		if (!isset($dba_record['schema'])) {
			self::Create($dba_schema);
			$sql = '
				INSERT INTO DBA (`request`, `schema`, `timestamp`)
				VALUES (:request, :schema, UNIX_TIMESTAMP())
			';
			
		} elseif ($dba_record['schema'] !== $dba_encoded) {
			self::Alter($dba_schema, $dba_record);
			$sql = '
				UPDATE DBA SET
					`schema` = :schema,
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = :request
			';
		}

		self::$db['sys']->perform($sql, array(
			'schema' => $dba_encoded,
			'request' => $dba_request,
		));
	}

	/**
	 * Create table from schema
	 *
	 * @param array $dba_schema
	 *  An associative array that mirrors the table sctructure
	 * @param string $db_name
	 *  name of the database to perform the operation
	 *
	 * @return bool $result
	 */
	static function Create($dba_schema, $db_name = 'core') {

		foreach ($dba_schema as $name => $table) {
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

			self::$db[$db_name]->perform($dba_sql);

		}

	}

	/**
	 * Alter a table from schema
	 *
	 * THIS METHOD IS BROKEN!!1!!
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

		$table = $schema_new['name'];
		$dba_sql = array();

		$columns = array();

		foreach ($schema_new as $table => $table_schema) {

			// if the table already exist
			if (isset($schema_old[$table])) {
				foreach ($table_schema['column'] as $column_name => $column) {

					if (!isset($schema_old[$table]['column'][$column_name])) {
						$dba_sql[] = '
							ADD ' . self::create_column_sql($column_name, self::process_column($column)) .
						"\n";

					} elseif ($schema_new[$table]['column'][$column_name] != $schema_old[$table]['column'][$column_name]) {
						$dba_sql[] = '
							CHANGE `' .$column_name . '` ' .
							self::create_column_sql($column_name, self::process_column($column)) .
						"\n";
					}

				}
			}

			$dba_sql = array_merge($dba_sql, self::alter_key('primary', $schema_new[$table], $schema_old[$table]));
			$dba_sql = array_merge($dba_sql, self::alter_key('unique', $schema_new[$table], $schema_old[$table]));
			$dba_sql = array_merge($dba_sql, self::alter_key('index', $schema_new[$table], $schema_old[$table]));


			$sql = 'ALTER TABLE ' . $schema_new[$table] . ' ' .	implode(', ', $dba_sql);

			self::$db['core']->perform($sql);
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
