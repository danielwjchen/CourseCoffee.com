<?php

interface DAOInterface {

	/**
	 * Create an object in database
	 *
	 * @params $params
	 *  an associative array of params that defines the new object
	 */
	public function create($params) ;

	/**
	 * Read an object in database
	 *
	 * @param $params
	 *  an associative array of params to be executed with the SQL query
	 */
	public function read($params) ;

	/**
	 * Update the object in database
	 */
	public function update() ;

	/**
	 * Destroy the object in database
	 *
	 * @param string $params
	 *  a value that identifies the object
	 */
	public function destroy() ;
}
