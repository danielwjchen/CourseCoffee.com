<?php

require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAttributeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/DateDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDateLinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';

class QuestDateRangeDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Override DAOSetup::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		$quest = array();

		if (isset($params['specified'])) {
			$quest['record']['id'] = $params['specified']['id'];

		} else {
			$quest = QuestDAOSetup::Prepare();

		}

		self::truncateTable('date');
		self::truncateTable('date_type');
		self::truncateTable('quest_date_linkage');
		$end_date = DateDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'end_date',
			),
		));
		$linkage = QuestDateLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $end_date['record']['id']
			)
		));
		$begin_date = DateDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'begin_date',
			),
		));
		$linkage = QuestDateLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $begin_date['record']['id']
			)
		));
		$params = array(
			'quest_id' => $quest['record']['id'],
			'begin_date' => $begin_date['params']['timestamp'],
			'end_date' => $end_date['params']['timestamp'],
		);
		$record = array(
			'quest_id' => $quest['record']['id'],
			'begin_date' => $begin_date['record']['timestamp'],
			'end_date' => $end_date['record']['timestamp'],
		);
		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		QuestDAOSetup::CleanUp();
		QuestAttributeDAOSetup::CleanUp();
		DateDAOSetup::CleanUp();
		QuestAffiliationLinkageDAOSetup::CleanUp();
	}

}
