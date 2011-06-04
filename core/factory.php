<?php

/**
 * Includes necessary file in order to create object from a class
 */
class Factory{

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
	public static function __callStatic($type, $params = NULL) {
		$className = ucfirst($params[0]).$type;
		try {
			require_once('../' . strtolower($type) . '/' . ucfirst($params[0]) . $type. '.php');
		} catch (Exception $e) {
		}
		switch ($type) {
			case 'DAO':
				$result = new $className($params[1]);
				break;
			case 'MODEL':
				break;
			case 'CONTROLLER':
				break;
			case 'VIEW':
				break;
			default:
			echo 'shit happened';
		}
	}
}
