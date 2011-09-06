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
