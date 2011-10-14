<?php
/**
 * @file
 * Define the database schema to be created in a Schema request
 */
interface SchemaInterface {

	/**
	 * Get the database which the schema will be appied to.
	 *
	 * @return array
	 */
	public function getDB() ;

	/**
	 * Get the definition of a database schema
	 *
	 * @return array $definition
	 *  an associative array that uses keys and values to define a table
	 *  - name
	 *  - columns
	 *  - key (optional)
	 */
	public function getDefinition();
}

/**
 * Base class for child Schema classes that declare their tables in the default
 * database.
 */
class DefaultSchema {
	/**
	 * Get default database
	 */
	public function getDB() {
		return array('default');
	}
}

/**
 * Base class for child Schema classes that declare their tables in institution
 * databases.
 */
class InstitutionSchema {
	/**
	 * Get institution databases
	 */
	public function getDB() {
		global $config;
		return array_keys($config->db['institution']);
	}
}

/**
 * Base class for all SchemaInterface inplementation
 */
abstract class Schema {

	/**
	 * Singleton instance
	 */
	public static $instance;

	/**
	 * an associative array of PDO database connection
	 */
	private $db;

	/**
	 * 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	function __construct($config_db) {
		$this->db['default'] = new DB($config_db['default']);
		foreach ($config_db['institution'] as $name => $connection) {
			$this->db[$name] = new DB($connection);
		}
		

	}

	function __destruct() {
		$this->db = null;
	}

	/**
	 * This maps a generic data type in combination with its data size
	 * to the engine-specific data type.
	 *
   * Put :normal last so it gets preserved by array_flip.  This makes
   * it much easier for modules (such as schema.module) to map
   * database types back into schema types.
	 */
	private function type_map($type) {
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
	private function process_column($column) {

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
	private function create_column_sql($name, $spec) {
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
	private function create_key_sql($keys) {
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
	 * @param string $schema_path
	 *  an optional string to specify the Schema file path if different from default
	 *
	 * @return array $schema_sql
	 *  an array of key clause SQL queries
	 */
	private function alter_key_sql($table_new, $table_old) {
		$schema_sql  = array();

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
					$schema_sql[] = ' DROP ' . $sql_key . ' `' . $drop_key . '` ';
				} else {
					$schema_sql[] = ' DROP ' . $sql_key .' ' . implode(', ', $drop_key) . '';
				}
			}

			if (!empty($add_key)) {
				if ($type == 'primary' ) {
					$schema_sql[] = ' ADD ' . $sql_key . ' `' . $add_key . '` ';
				} else {
					foreach ($add_key as $key_name) {
						$schema_sql[] = 'ADD ' . $sql_key .' `' . $key_name . '` (' . implode(', ', $table_new[$type][$key_name]) . ')';
					}
				}
			}
		}
		return $schema_sql;
	}


	/**
	 * Rebuild Schema schema array from actual table schema
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
	 * Initialize an Emailer instance
	 *
	 * This checks if an instance of this class already exists
	 */
	public static function Init() {
		global $config;
		if (self::$instance == null) {
			self::$instance = new static($config->db);
		}
	}


	/**
	 * Process a Schema Request.
	 *
	 * @param string $schema_request
	 *  name of the Schema request
	 * @param string $schema_path
	 *  a string to specify the Schema file path 
	 *
	 * @return bool $result
	 */
	static function Request($schema_request, $schema_path) {
		require_once $schema_path;

		$schema_object     = new $schema_request();
		$schema_definition = $schema_object->getDefinition();
		$schema_database   = $schema_object->getDB();

		$schema_record = self::$db['default']->fetch(
			'SELECT * FROM `Schema` WHERE request = :request',
			array('request' => $schema_request)
		);

		$encoded_schema = json_encode($schema_definition);

		$sql = '';
		if (empty($schema_record['schema'])) {
			self::Create($schema_database, $schema_definition);
			$sql = '
				INSERT INTO `Schema` (`request`, `schema`, `timestamp`)
				VALUES (:request, :schema, UNIX_TIMESTAMP())
			';
			
		} else {
			self::Alter($schema_database, $schema_definition, json_decode($schema_record['schema'], true));
			$sql = '
				UPDATE `Schema` SET
					`schema` = :schema,
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = :request
			';
		}
		self::$db['default']->perform($sql, array(
			'schema'  => $encoded_schema,
			'request' => $schema_request,
		));

		$encoded_script = '';
		if (method_exists($schema_request, 'script')) {
			$schema_sql = call_user_func($schema_request . '::script');
			$encoded_script = json_encode($schema_sql);
			if (empty($schema_record['script']) || $schema_record['script'] !== $encoded_script) {
				foreach($schema_sql as $sql) {
					self::$db['default']->perform($sql);
				}
			} else {
				$encoded_script = $schema_record['script'];
			}
			self::$db['default']->perform("
				UPDATE `Schema` SET
					`script` = :script,
					`timestamp` = UNIX_TIMESTAMP()
				WHERE `request` = :request
				",
				array(
					'script'  => $encoded_script,
					'request' => $schema_request,
				)
			);
		}

	}

	/**
	 * Create table
	 *
	 * This is a helper method to share code among Create() and Alter()
	 *
	 * @param array $db
	 *  An array of database where the tables will be created
	 * @param string $name
	 *  name of the table
	 * @param array $table
	 *  An associative array that mirrors the table sctructure
	 */
	private static function CreateTable($db, $name, $table) {
		global $config;

		$schema_sql = 'CREATE TABLE IF NOT EXISTS `' . $name . '` (';

		// compose columns
		$columns = array();
		foreach ($table['column'] as $column_name => $column) {
			$columns[] = self::create_column_sql($column_name, self::process_column($column));
		}
		$schema_sql .= implode(", \n", $columns) . ", \n";

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
		
		$schema_sql .= implode(', ', $keys) .')';
		foreach ($db as $db_name) {
			echo $db_name . "\n";
			self::$db[$db_name]->perform($schema_sql);
		}
	}

	/**
	 * Create table from schema
	 *
	 * @param array $schema_database
	 *  An array of database where the tables will be created
	 * @param array $schema_definition
	 *  An array of table schemas that mirrors the table sctructure
	 */
	public static function Create($schema_database, $schema_definition) {

		foreach ($schema_definition as $name => $table) {
			self::CreateTable($schema_database, $name, $table);

		}

	}

	/**
	 * Alter a table from schema
	 *
	 * This method compares the new schema with the old (existing) one and computes
	 * a series of queries the modify the table. It doesn't handle DROP operation.
	 *
	 * @param array $schema_database
	 *  An array of database where the tables will be created
	 * @param array $schema_new
	 *  An associative array that mirrors the table sctructure
	 * @param array $schema_old
	 *  An associative array that mirrors the table sctructure
	 * @param string $schema_path
	 *  an optional string to specify the Schema file path if different from default
	 *
	 * @return bool $result
	 */
	public static function Alter($schema_database, $schema_new, $schema_old) {
		global $config;

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
				self::CreateTable($schema_database, $table_name, $schema_new[$table_name]);
			}
		}

		$drop_tables = array_diff($exist_tables, $update_tables);
		if (!empty($drop_tables)) {
			foreach ($drop_tables as $table_name) {
				foreach ($schema_database as $db_name) {
					self::$db[$db_name]->perform('DROP TABLE `' . $table_name . '`');
				}
			}
		}
		if (!empty($update_tables)) {

			foreach ($update_tables as $table_name) {
				$request_cols = array_keys($schema_new[$table_name]['column']);
				$exist_cols   = array_keys($schema_old[$table_name]['column']);
				$update_cols  = array_intersect($request_cols, $exist_cols);
				$drop_cols    = array_diff($exist_cols, $update_cols);
				$add_cols     = array_diff($request_cols, $update_cols);

				$schema_sql = array();

				foreach ($drop_cols as $col_name) {
					$schema_sql[] = 'DROP ' . $col_name . "\n";
				}

				foreach ($add_cols as $col_name) {
					$schema_sql[] = '
						ADD ' . 
						self::create_column_sql($col_name, self::process_column($schema_new[$table_name]['column'][$col_name])) .
					"\n";
				}

				foreach ($update_cols as $col_name) {
					$schema_sql[] = '
						CHANGE `' .$col_name . '` ' .
						self::create_column_sql($col_name, self::process_column($schema_new[$table_name]['column'][$col_name])) .
					"\n";
				}

				$schema_sql = array_merge($schema_sql, self::alter_key_sql($schema_new[$table_name], $schema_old[$table_name]));

				$sql = 'ALTER TABLE `' . $table_name . '` ' . implode(', ', $schema_sql);

				foreach ($schema_database as $db_name) {
					self::$db[$db_name]->perform($sql);
				}
			}
		}

	}

}
