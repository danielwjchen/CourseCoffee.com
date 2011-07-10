<?php

require_once TEST_CORE_DAO_PATH . '/DAOSetup.php';

/**
 * Base class for all AttributeDAOSetup
 */
abstract class AttributeDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * name of the dao that has this attribute
	 */
	protected static $dao_class;

	/**
	 * Extend DAOSetup::Prepare()
	 */
  public static function Prepare(array $params = array('random' => true)) {
		parent::Prepare($params);
		$called_class = get_called_class();
		$dao_class = $called_class::$dao_class;
		$dao = array();
		$dao_id = strtolower(str_replace('DAOSetup', '', $dao_class)) . '_id';

		if (isset($params['id'])) {
			$dao['record']['id'] = $params['id'];
		
		} else {
			require_once TEST_CORE_DAO_PATH . '/setups/' . $dao_class . '.php';
			$dao = $dao_class::Prepare();
		}

		$type_class = str_replace(
			'Attribute', 
			'AttributeType', 
			$called_class
		);

		$attribute_type = '';
		require_once TEST_CORE_DAO_PATH . '/setups/' . $type_class . '.php';
		if (isset($params['type'])) {
			$attribute_type = $type_class::Prepare($params['type']);

		} else {
			$attribute_type = $type_class::Prepare();

		}

		$params = array(
			'value' => self::generateString(16),
			$dao_id => $dao['record']['id'],
			'type_id' => $attribute_type['record']['id'],
			'type' => $attribute_type['record']['name']
		);

		$table_name = $called_class::getTableName($called_class);

		self::$db->perform("
			INSERT INTO {$table_name} ({$dao_id}, value, type_id)
			VALUES (
				:{$dao_id},
				:value, 
				(SELECT id FROM {$table_name}_type WHERE name = :type)
			)",
			array(
				$dao_id => $params[$dao_id],
				'value' => $params['value'],
				'type' => $params['type']
			)
		);

		$record  = self::$db->fetch("
			SELECT 
				a.*,
				at.name AS type,
				at.id AS type_id
			FROM `{$table_name}` a
			INNER JOIN `{$table_name}_type` at
				ON a.type_id = at.id
			WHERE	a.value = :value", 
			array('value' => $params['value'])
		);
		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Extend DAOSetup::CleanUp()
	 */
	public static function CleanUp() {
		parent::CleanUp();
		
		// clean up the dao
		$called_class = get_called_class();
		$dao_class = str_replace('Attribute', '', $called_class);
		require_once TEST_CORE_DAO_PATH . '/setups/' . $dao_class . '.php';
		$dao_class::CleanUp();

		// clean up attribute type
		$type_class = str_replace(
			'Attribute', 
			'AttributeType', 
			$called_class
		);
		require_once TEST_CORE_DAO_PATH . '/setups/' . $type_class . '.php';
		$type_class::CleanUp();
	}
}
