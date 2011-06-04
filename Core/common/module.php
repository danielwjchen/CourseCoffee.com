<?php

class Module{
	/**
	 * Invoke an module and construct an object
	 *
	 * @param $type
	 *  type of class, possibly values dao, controller, model, or view
	 * @param $name
	 *  name of the class
	 * @param $data
	 *  an associative array of possible data
	 *
	 * @return $result
	 *  the object constructed from class
	 */
	static function invoke(string $type, string $name, array $params = NULL) {
	}
	public function install() {
	}
}
