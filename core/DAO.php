<?php

/**
 * Construct objects from database records.
 */
abstract class DAO{

	/**
	 * Define the table structure
	 */
	protected $structure;

	/**
	 * Stores values in an associative array
	 *  - #primary: The name of the primary table. The id of the primary table will 
	 *    be used as the foreign key to link other tables to construct an object
	 *  - #extend: (optional) The name of extension table(s).
	 */
	private $data;

	/**
	 * Construct a DAO for child classes
	 *
	 * @param $structure
	 *  an associative array that defines an object in the database
	 */
	public function __construct(array $structure) {
		$this->structure = $structure;
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
		try {
			if (isset($this->data[$name])) {
				$result = $this->data[$name];
			} elseif ($column == $name) {
				$this->selectQuery($table, $column);
				$this->data[$name] = $result;
			} else {
				$result = false;
			}
		} catch (Exception $e) {
		}
		return $result;
	}

	/**
	 * Set the value of the object's attribute
	 *
	 * WARNING! this operation won't alter the value in table unless save() is run
	 */
	public function __set($name, $value) {
	}


	/**
	 * Generate a selection query
	 */
	private function selectQuery(string $table, string $column) {
		return "
			SELECT {$column} FROM {$table}
			WHERE id = {$this->data['id']}
		";
	}

	/**
	 * Install the table from definition.
	 */
	protected function install() {
	}

	public function save() {
	}
}
