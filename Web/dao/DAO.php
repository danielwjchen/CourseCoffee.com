<?php
/**
 * @file
 * Construct objects from database records and handle all your C.R.U.D. needs 
 * with care and love.
 */

interface DAOInterface {

	/**
	 * Create an object in database
	 *
	 * @params array $params
	 *  an associative array of params that defines the new object
	 *
	 * @return mixed
	 *  the id of the record created or false on failure
	 */
	public function create($params) ;

	/**
	 * Read an object in database
	 *
	 * @param array $params
	 *  an associative array of params to be executed with the SQL query
	 *   - id: optional, this will get a particular record
	 */
	public function read($params) ;

	/**
	 * Update the object in database
	 */
	public function update() ;

	/**
	 * Destroy the object in database
	 */
	public function destroy() ;
}

/**
 * Some shared methods among all dao classes
 */
abstract class DAO{

	/**
	 * Define attributes of the object.
	 *
	 * It also stores values in an associative array
	 */
	protected $attr;

	/**
	 * keep a list of records constructed from database
	 */
	protected $list;

	/**
	 * Database connection object
	 */
	protected $db;

	/**
	 * Define the attributes of the dao object.
	 *
	 * @return array
	 */
	abstract protected function defineAttribute() ;


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
	 * @return bool
	 */
	protected function updateAttribute($data) {
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
	 */
	function __construct($db) {
		$this->db = $db;
		$this->setAttribute($this->defineAttribute());
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
	 *  the value of the attribute or list records found
	 */
	public function __get($name) {
		$result = null;

		if ($name == 'list') {
			$result = $this->list;

		} elseif ($name == 'attribute') {
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
	 * WARNING! this operation won't alter the value in table unless update() is 
	 * run. Also note that it only works with attributes
	 */
	public function __set($name, $value) {
		$this->attr[$name] = $value;
	}
}
