<?php
/**
 * Manage tabale schemas
 */
abstract class Schema{
	
	/**
	 * Define the structure of a table
	 *
	 * @return array $result
	 *  An associative array that mirrors the table sctructure
	 */
	abstract function Install($schema) {
	}

}
