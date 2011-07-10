<?php

require_once TEST_CORE_DAO_PATH . '/setups/CollegeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/LocationDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/AffiliationLocationLinkageDAOSetup.php';

class CollegeCampusDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Override DAOSetup::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		self::truncateTable('affiliation');
		self::truncateTable('location');
		$college = CollegeDAOSetup::Prepare();
		$location_type = LocationTypeDAOSetup::Prepare(array('specified' => 'college_campus'));

		$params = array(
			'college' => $college['record']['name'],
			'college_id' => $college['record']['id'],
			'name' => self::generateString(32),
			'longitude' => mt_rand(0, 15),
			'latitude' => mt_rand(0, 15),
			'type' => $location_type['record']['name'],
			'type_id' => $location_type['record']['id'],
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

		$record = self::$db->fetch("
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

		AffiliationLocationLinkageDAOSetup::Prepare(array(
			'specified' => array(
				'parent_id' => $college['record']['id'],
				'child_id' => $record['id'],
			)
		));

		$record['college_id'] = $params['college_id'];
		$record['college'] = $params['college'];

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		self::truncateTable('location');
		self::truncateTable('affiliation');
		AffiliationTypeDAOSetup::CleanUp();
		AffiliationLocationLinkageDAOSetup::CleanUp();
	}

}
