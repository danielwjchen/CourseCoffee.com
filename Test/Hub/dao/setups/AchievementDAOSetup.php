<?php

require_once TEST_CORE_DAO_PATH . '/setups/AchievementTypeDAOSetup.php';

class AchievementDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$type = array();

		if (isset($params['type'])) {
			$type = AchievementTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = AchievementTypeDAOSetup::Prepare();

		}

		$new_params = array(
			'name' => self::generateString(128),
			'metric' => mt_rand(0, 100),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform(
			'INSERT INTO `achievement` 
				(name, metric, type_id)
				VALUES (
					:name,
					:metric,
					(SELECT `id` FROM `achievement_type` WHERE name = :type)
					)',
			array(
				'name' => $new_params['name'],
				'metric' => $new_params['metric'],
				'type' => $new_params['type'],
			)
		);

		$record = self::$db->fetch("
			SELECT 
				a.*,
				t.name AS type,
				t.id AS type_id
			FROM `achievement` a
			INNER JOIN `achievement_type` t
				ON a.type_id = t.id
			WHERE a.name = :name",
			array('name' => $new_params['name'])
		);

		return array('record' => $record, 'params' => $new_params);
	}

	/**
	 * Implements SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		AchievementTypeDAOSetup::CleanUp();
	}

}

