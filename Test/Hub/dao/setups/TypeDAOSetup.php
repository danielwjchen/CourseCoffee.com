<?php

require_once TEST_CORE_DAO_PATH . '/DAOSetup.php';

abstract class TypeDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * An array of values that define a type of the client class.
	 */
	static $typeArray;

	/*
	 * Generate randomized types.
	 *
	 * @oaram array $mode
	 *  a flag to dictate how the type(s) are generated
	 *
	 * @return string
	 *  name of the type
	 */
	protected static function generateType(array $mode = array('random' => true)) {
		$called_class = get_called_class();

		if (isset($mode['specified'])) {
			return array($mode['specified']);

		} elseif (isset($mode['all'])) {
			return $called_class::$typeArray;

		} else {
			$key = array_rand($called_class::$typeArray);
			return array($called_class::$typeArray[$key]);

		} 

	}

	/**
	 * Extend DAOSetup::Prepare()
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$called_class = get_called_class();
		$type = self::generateType($params);
		$new_params = null;
		$record = null;


		for ($i = 0; $i < count($type); $i++) {
			$new_params['name'] = $type[$i];
			self::$db->perform(
				"INSERT INTO `" . $called_class::$table_name ."` (name) VALUE (:name)",
				array('name' => $new_params['name'])
			);

			$record = self::$db->fetch(
				"SELECT * FROM `" . $called_class::$table_name . "` WHERE `name` = :name",
				array('name' => $new_params['name'])
			);

		}


		if (isset($params['all'])) {
			$key = array_rand($called_class::$typeArray);
			$new_params['name'] = array($called_class::$typeArray[$key]);

		}

		return array('record' => $record, 'params' => $new_params);
	}

}
