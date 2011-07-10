<?php

require_once TEST_CORE_DAO_PATH . '/setups/LocationTypeDAOSetup.php';

class LocationDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$type = array();
		if (isset($params['type'])) {
			$type = LocationTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = LocationTypeDAOSetup::Prepare();

		}

		$params = array(
			'name' => self::generateString(64),
			'longitude' => mt_rand(0, 15),
			'latitude' => mt_rand(0, 15),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform(
			'INSERT INTO `location` 
				(name, longitude, latitude, type_id)
			VALUES
				(:name, :longitude, :latitude, :type_id)',
			array(
				'name' => $params['name'],
				'longitude' => $params['longitude'],
				'latitude' => $params['latitude'],
				'type_id' => $params['type_id']
			)
		);

		$record  = self::$db->fetch("
			SELECT 
				l.*,
				lt.name AS type,
				lt.id AS type_id
			FROM `location` l
			INNER JOIN `location_type` lt
				ON l.type_id = lt.id
			WHERE	l.name = :name", 
			array('name' => $params['name'])
		);
		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Extend SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		LocationTypeDAOSetup::CleanUp();
	}

}
