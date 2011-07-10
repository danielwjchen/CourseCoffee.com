<?php

require_once TEST_CORE_DAO_PATH . '/setups/StatisticTypeDAOSetup.php';

class StatisticDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$type = array();

		if (isset($params['type'])) {
			$type = StatisticTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = StatisticTypeDAOSetup::Prepare();

		}

		$new_params = array(
			'data' => mt_rand(0, 100),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform(
			'INSERT INTO `statistic` 
				(data, type_id)
			VALUES (
					:data,
					(SELECT `id` FROM `statistic_type` WHERE name = :type)
				)',
			array(
				'data' => $new_params['data'],
				'type' => $new_params['type'],
			)
		);

		$record = self::$db->fetch("
			SELECT 
				s.*,
				st.name AS type,
				st.id AS type_id
			FROM `statistic` s
			INNER JOIN `statistic_type` st
				ON s.type_id = st.id
			WHERE s.data = :data",
			array('data' => $new_params['data'])
		);

		return array('record' => $record, 'params' => $new_params);
	}

	/**
	 * Implements SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		StatisticTypeDAOSetup::CleanUp();
	}

}

