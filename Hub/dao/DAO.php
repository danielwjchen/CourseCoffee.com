<?php

/**
 * Construct objects from database records and handle all your C.R.U.D. needs 
 * with care and love.
 */
abstract class DAO{

	/**
	 * Define attributes of the object.
	 *
	 * It also stores values in an associative array
	 */
	protected $attr;

	/**
	 * Database connection object
	 */
	protected $db;


	/**
	 * Re-write the parameter array to be bond in a PDO statement.
	 *
	 * @param array $params
	 *  an associative array of parameters
	 *
	 * @return array $result
	 *  an associative array of params to be executed with the SQL query
	 */
	protected function rewriteParams($params) {
		$result = array();

		foreach ($params as $name => $value) {
			$result[':' . $name] = $value;
		}

		return $result;
	}

	/**
	 * Set the object attribute
	 *
	 * @param array $attribute
	 *  an associative array of object attributes
	 */
	protected function setAttribute($attribute) {
		$this->attr = array_fill_keys($attribute, '');

	}

	/**
	 * Update the object Attribute
	 *
	 * @param array $data
	 *  an assciative array that contains data
	 *
	 * @
	 */
	protected function updateAttrribute($data) {
		if (!empty($data)) {
			foreach ($this->attr as $key => $value) {
				$this->attr[$key] = isset($data[$key]) ? $data[$key] : null;

			}

			return true;

		} else {
			return false;

		}

	}

	/**
	 * Extend the object attribute
	 *
	 * @param array $additional
	 *  an array of new attributes to be expected
	 */
	protected function extendAttribute($additional) {
		$this->attr = array_merge($this->attr, array_fill_keys($additional, ''));

	}

	/**
	 * Load a the data for child classes
	 *
	 * @param $db
	 *  a database object
	 * @param array $attr
	 *  an associative array of object attributes
	 * @param array $params
	 *  an associative array of params to be executed with the SQL query
	 */
	function __construct($db, $attr, $params = NULL) {
		$this->setAttribute($attr);
		$this->db = $db;

		if (!empty($params)) {
			$this->read($params);

		}

	}

  /**
   * Release DB connection during destruct.
   */
  function __destruct() {
    $this->db = null;
  }

	/**
	 * Get the value of the object's attribute
	 *
	 * @param $name
	 *  name of thattribute
	 *
	 * @return $result
	 *  the value of the attribute
	 */
	public function __get($name) {
		$result = null;

		if ($name == 'attribute') {
			$result = $this->attr;

		} elseif (isset($this->attr[$name])) {
			$result = $this->attr[$name];

		} else {
			throw new Exception('attemp to access object attribute that does not exist - ' . $name);

		}

		return $result;
	}

	/**
	 * Set the value of the object's attribute
	 *
	 * WARNING! this operation won't alter the value in table unless save() is run
	 */
	public function __set($name, $value) {

		if (!isset($this->attr[$name])) {
			throw new Exception('unknown object attribute ' . __CLASS__ . '::' . $name);

		} else {
			$this->attr[$name] = $value;

		}
	}

	/**
	 * Create an object in database
	 *
	 * @param string $sql
	 *  the SQL query to modify the record
	 * @params $params
	 *  an associative array of params that defines the new object
	 */
	protected function create($sql, $params) {
		$fixedParams = $this->rewriteParams($params);
		return $this->db->insert($sql, $fixedParams);

	}

	/**
	 * Read an object in database
	 *
	 * @param $sql
	 *  a SQL query to construct the object from database
	 * @param $params
	 *  an associative array of params to be executed with the SQL query
	 */
	protected function read($sql, $params) {
		$fixed_params = $this->rewriteParams($params);
		return $this->db->fetch($sql, $fixed_params);
	}

	/**
	 * Update the object in database
	 *
	 * @param string $sql
	 *  the SQL query to modify the record
	 * @param $params
	 *  an associative array of params to be executed with the SQL query
	 */
	protected function update($sql, $params = NULL) {
		$params = empty($params) ? $this->attr : $params;
		$fixedParams = $this->rewriteParams($params);
		$this->db->perform($sql, $fixedParams);
	}

	/**
	 * Destroy the object in database
	 *
	 * @param string $sql
	 *  the SQL query to modify the record
	 * @param string $params
	 *  a value that identifies the object
	 */
	 protected function destroy($sql, $params) {

		$params = $this->rewriteParams($params);
		$this->db->perform($sql, $params);

		foreach ($this->attr as $name => $value) {
			$this->attr[$name] = null;
		}

	 }
}
