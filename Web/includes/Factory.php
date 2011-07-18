<?php

/**
 * Includes necessary file in order to create object from a class
 */
class Factory{

	static private $db;

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
		$class_name = $params[0] . $type;

		switch ($type) {
			case 'DAO':
				require_once DAO_PATH . 'DAO.php';
				if (preg_match('/college/i', $class_name)) {
					require_once DAO_PATH . '/college/' . $class_name . '.php';

				} elseif (preg_match('/oauth2/i', $class_name)) {
					require_once DAO_PATH . '/oauth2/' . $class_name . '.php';

				} else {
					require_once DAO_PATH . '/core/' . $class_name . '.php';

				}

        if (isset($params[1])) {
          $result = new $class_name(self::$db, $params[1]);

        } else {
          $result = new $class_name(self::$db);

        }

				break;

			case 'Model':
				require_once MODEL_PATH . 'Model.php';
				require_once MODEL_PATH . '/' . $class_name . '.php';
				$result = new $class_name();
				break;

			case 'Controller':
				require_once CONTROLLER_PATH . 'ControllerInterface.php';
				require_once CONTROLLER_PATH . 'Controller.php';
				if (preg_match('/college/i', $class_name)) {
					require_once CONTROLLER_PATH . '/college/' . $class_name . '.php';
				} else {
					require_once CONTROLLER_PATH . '/core/' . $class_name . '.php';
				}
				$result = new $class_name();
				break;

			case 'View':
				require_once VIEW_PATH . 'View.php';
				if (preg_match('/college/i', $class_name)) {
					require_once VIEW_PATH . '/college/' . $class_name . '.php';
				} else {
					require_once VIEW_PATH . '/core/' . $class_name . '.php';
				}
				$result = new $class_name();
				break;

				break;
			default:
				throw new Exception('unknown object ' . $type);
		}

		return $result;
	}
}
