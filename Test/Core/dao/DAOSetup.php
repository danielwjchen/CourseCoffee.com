<?php

require_once TEST_CORE_PATH . '/CoreSetup.php';

/**
 * Set up the environment for testing DAO.
 */
class DAOSetup extends CoreSetup {

	static private $stage;

  /**
   * Extend CoreSetup::Init()
   */
  static public function Init($db_config) {
    parent::Init($db_config);
  }

	/**
	 * Extend CoreSetup::Prepare()
	 */
  static public function Prepare($stage) {
		$params = false;
		$object = false;

		switch ($stage) {
			case 'location_type':
				$params = array('name' => 'college_campus');
				self::$db->perform(
					'INSERT INTO `location_type` (name) VALUE (:name)',
					array('name' => $params['name'])
				);

				$record = self::$db->fetch(
					'SELECT * FROM `location_type` WHERE `name` = :name',
					array('name' => $params['name'])
				);

				break;

			case 'location':
				$params = array(
					'name' => 'East Lansing',
					'longitude' => mt_rand(12, 15),
					'latitude' => mt_rand(12, 15),
					'type' => '',
					'type_id' => '',
				);

				$type = self::Prepare('location_type');
				$params['type'] = $type['record']['name'];
				$params['type_id'] = $type['record']['id'];

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
				break;

			case 'statistic':
			case 'date':
			case 'affiliation_type':
				$params = array('name' => 'college');
				self::$db->perform(
					'INSERT INTO `affiliation_type` (name) VALUE (:name)', $params
				);

				$record = self::$db->fetch(
					'SELECT * FROM `affiliation_type` WHERE name = :name', $params
				);
				break;

			case 'affiliation':
				$params = array(
					'name' => 'Department of Science and Enginerring',
					'url' => mt_rand(12, 15),
					'type' => '',
					'type_id' => '',
				);

				$type = self::Prepare('affiliation_type');
				$params['type'] = $type['params']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform(
					'INSERT INTO `affiliation` (name, url, type_id) 
						VALUES (
							:name, 
							:url,
							(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
							)',
					array(
						'name' => $params['name'],
						'url' => $params['url'],
						'type' => $params['type'],
					)
				);

				$record = self::$db->fetch("
					SELECT 
						a.*,
						t.name AS type,
						t.id AS type_id
					FROM `affiliation` a
					INNER JOIN `affiliation_type` t
							ON a.type_id = t.id
						WHERE a.name = :name",
					array('name' => $params['name']));
				break;

			case 'affiliation_location_linkage':
				$affiliation = self::Prepare('affiliation');
				$location = self::Prepare('location');
				$params = array(
					'affiliation_id' => $affiliation['record']['id'],
					'location_id' => $location['record']['id']
				);

				self::$db->perform(
					"INSERT INTO `affiliation_location_linkage` (affiliation_id, location_id) 
						VALUES (:affiliation_id, :location_id)",
					$params);

				$record = self::$db->fetch(
					"SELECT * FROM `affiliation_location_linkage` 
						WHERE affiliation_id = :affiliation_id AND location_id = :location_id", 
					$params);
				break;

			case 'user':
			case 'person':
			case 'quest':
			default:
				throw new Exception('unknown stage - ' . $stage);
		}

		return array('params' => $params, 'record' => $record);;
  }

  static public function CleanUp($stage) {
		switch ($stage) {
			case 'location_type':
			case 'affiliation_type':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				break;

			case 'affiliation':
			case 'location':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				self::CleanUp("{$stage}_type");
				break;

			case 'affiliation_location_linkage':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				$table = explode('_', $stage);
				self::CleanUp($table[0]);
				self::CleanUp($table[1]);
				break;

			case 'date':
			case 'statistic':
			case 'user':
			case 'person':
			case 'quest':
			default:
				throw new Exception('unknown stage - ' . $stage);
		}
  }
}
