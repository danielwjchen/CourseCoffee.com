<?php

/**
 * Includes necessary file in order to create object from a class
 */
class Factory{

	static private $db;

	static private function getClassName($string) {
		$array = explode('_', $string);
		if (count($array) == 1) {
			return ucfirst($array[0]);
		}

		$result = '';

		foreach ($array as $key) {
			$result .= ucfirst($key);
		}

		return $result;
	}

	/**
	 * Initiate the factory
	 *
	 * @param array $db_config
	 *  an associative array that defines the configuration of a database 
	 *  connection
	 */
	static public function Init($db_config) {
		self::$db = new DB($db_config);
	}

	/**
	 * Include file and create object.
	 *
	 * @param string $type
	 *  The type of the class object/file to be created and included
	 * @param array $params
	 *  the parameters to be passed to construct the object. Default to null.
	 *
	 * @return object $result
	 *  the object constructed from requested class
	 */
	static public function __callStatic($type, $params = NULL) {
		$className = self::getClassName($params[0]).$type;

		try {
			require_once ROOT_PATH . strtolower($type) . '/' . $className . '.php';

		} catch (Exception $e) {

			echo $e->getMessage();
		}

		switch ($type) {
			case 'DAO':
				$result = new $className(self::$db, $params[1]);
				break;
			case 'MODEL':
				break;
			case 'CONTROLLER':
				break;
			case 'VIEW':
				break;
			default:
				throw new Exception('unknown object ' . $type);
		}

		return $result;
	}
}
