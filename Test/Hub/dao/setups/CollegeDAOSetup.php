<?php

require_once TEST_CORE_DAO_PATH . '/setups/AffiliationTypeDAOSetup.php';

class CollegeDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		self::truncateTable('affiliation');
		self::truncateTable('affiliation_type');
		$type = AffiliationTypeDAOSetup::Prepare(array('specified' => 'college'));
		$params = array(
			'name' => self::generateString(64),
			'url' => self::generateString(64),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform('
			INSERT INTO `affiliation` 
				(name, url, type_id) 
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
			array('name' => $params['name'])
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		self::truncateTable('affiliation');
		AffiliationTypeDAOSetup::CleanUp();
	}

}
