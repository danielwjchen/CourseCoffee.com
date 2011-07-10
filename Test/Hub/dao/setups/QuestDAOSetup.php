<?php

require_once TEST_CORE_DAO_PATH . '/setups/UserDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestTypeDAOSetup.php';

class QuestDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$type = array();

		if (isset($params['type'])) {
			$type = QuestTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = QuestTypeDAOSetup::Prepare();

		}
		$user = UserDAOSetup::Prepare($params);

		$new_params = array(
			'user_id' => $user['record']['id'],
			'objective' => self::generateString(128),
			'description' => self::generateString(256),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform(
			'INSERT INTO `quest` 
				(user_id, objective, description, type_id)
				VALUES (
					:user_id,
					:objective,
					:description,
					(SELECT `id` FROM `quest_type` WHERE name = :type)
					)',
			array(
				'user_id' => $new_params['user_id'],
				'objective' => $new_params['objective'],
				'description' => $new_params['description'],
				'type' => $new_params['type'],
			)
		);

		$record = self::$db->fetch("
			SELECT 
				q.*,
				t.name AS type,
				t.id AS type_id
			FROM `quest` q
			INNER JOIN `quest_type` t
				ON q.type_id = t.id
			INNER JOIN `user` u
				ON q.user_id = u.id
			WHERE q.objective = :objective",
			array('objective' => $new_params['objective'])
		);

		return array('record' => $record, 'params' => $new_params);
	}

	/**
	 * Implements SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		UserDAOSetup::CleanUp();
		QuestTypeDAOSetup::CleanUp();
	}

}
