<?php


require_once TEST_CORE_DAO_PATH . '/setups/ItemTypeDAOSetup.php';

class ItemDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Implements SetupInterface::Prepare().
	 */
	public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$type = array();
		if (isset($params['type'])) {
			$type = ItemTypeDAOSetup::Prepare($params['type']);

		} else {
			$type = ItemTypeDAOSetup::Prepare();

		}

		$params = array(
			'name' => self::generateString(128),
			'type' => $type['params']['name'],
			'type_id' => $type['record']['id'],
		);

		self::$db->perform(
			'INSERT INTO `item` 
				(name, type_id)
				VALUES (
					:name,
					(SELECT `id` FROM `item_type` lt WHERE lt.name = :type)
					)',
			array(
				'name' => $params['name'],
				'type' => $params['type'],
			)
		);

		$record = self::$db->fetch("
			SELECT 
				i.*,
				t.name AS type,
				t.id AS type_id
			FROM `item` i
			INNER JOIN `item_type` t
				ON i.type_id = t.id
			WHERE i.name = :name",
			array('name' => $params['name'])
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Extend SetupInterface::CleanUp().
	 */
	static public function CleanUp() {
		parent::CleanUp();
		ItemTypeDAOSetup::CleanUp();
	}

}
