<?php

require_once TEST_CORE_DAO_PATH . '/setups/DateTypeDAOSetup.php';

class DateDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);

		if (isset($params['type'])) {
			$type = DateTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = DateTypeDAOSetup::Prepare();

		}

		$new_params = array(
			'timestamp' => mt_rand(time() - 1000, time() + 1000),
			'type' => $type['record']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform('
			INSERT INTO `date` (timestamp, type_id)
			VALUES (
				:timestamp, 
				(SELECT t.id FROM `date_type` AS t WHERE t.name = :type)
			)',
			array(
				'timestamp' => $new_params['timestamp'],
				'type' => $new_params['type']
			)
		);

		$record = self::$db->fetch("
			SELECT 
				d.*,
				dt.name AS type
			FROM `date` d
			INNER JOIN `date_type` dt
				ON d.type_id = dt.id
			WHERE	d.timestamp = :timestamp
				AND dt.name = :type", 
			array(
				'timestamp' => $new_params['timestamp'],
				'type' => $new_params['type']
			)
		);

		return array('record' => $record, 'params' => $new_params);
	}

	/**
	 * Implements SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		DateTypeDAOSetup::CleanUp();
	}

}
