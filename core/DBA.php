<?php
/**
 * Handle database administrative tasks.
 */
class DBAFactory{
	
	/**
	 * Create table from schema
	 *
	 * @param string $schema
	 *  name of the DBA request
	 *  An associative array that mirrors the table sctructure
	 *
	 * @return bool $result
	 */
	static function Install($schema) {
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
