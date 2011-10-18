<?php
/**
 * @file
 * Provide access to record list
 */

interface ListDAOInterface {
	/**
	 * Read an object in database
	 *
	 * @param array $params
	 *  an associative array of params to be executed with the SQL query
	 *   - range: optional, this will get a list of tasks based on range
	 *      - begin_date: optional
	 *      - end_date: optional
	 *   - limit: optional
	 *      - offset
	 *      - count
	 *   - like: optional, this would perform string matching
	 */
	public function read(array $params = null) ;

}

/**
 * Some shared methods among all dao classes
 */
abstract class ListDAO {

	/**
	 * keep a list of records constructed from database
	 */
	public $list;

	/**
	 * Database connection object
	 */
	protected $db;

	/**
	 * Load a the data for child classes
	 *
	 * @param $db
	 *  a database object
	 */
	function __construct($db) {
		$this->db = $db;
	}

	protected function setLimit($sql, $limit) {
		return $sql . " LIMIT {$limit['offset']}, {$limit['count']}";
	}

  /**
   * Release DB connection during destruct.
   */
  function __destruct() {
    $this->db = null;
  }

}
