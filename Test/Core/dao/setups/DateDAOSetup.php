<?php

class DateDAOSetup extends DAOSetup{

	/**
	 * Implements SetupInterface::Prepare().
	 */
	static public function Prepare($type = null) {
		$type = DAOSetup::Prepare('DateTypeDAO', $type);
		$params = array(
			'timestamp' => mt_rand(0, time()),
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
				'timestamp' => $params['timestamp'],
				'type' => $params['type']
			)
		);

		$record  = self::$db->fetch("
			SELECT 
				d.*,
				dt.name AS type,
				dt.id AS type_id
			FROM `date` d
			INNER JOIN `date_type` dt
				ON d.type_id = dt.id
			WHERE	d.timestamp = :timestamp", 
			array('timestamp' => $params['timestamp'])
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Implements SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		self::$db->perform("TRUNCATE TABLE `date`");
		self::CleanUp("date_type");
	}

}
