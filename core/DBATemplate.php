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
