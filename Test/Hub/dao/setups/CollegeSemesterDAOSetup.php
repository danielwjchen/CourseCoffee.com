<?php

require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/CollegeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDateRangeDAOSetup.php';

class CollegeSemesterDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Override DAOSetup::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		self::truncateTable('quest');

		$college = CollegeDAOSetup::Prepare();
		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'specified' => 'college_semester',
			),
		));

		$quest_date_range = QuestDateRangeDAOSetup::Prepare(array(
			'specified' => array(
				'id' => $quest['record']['id'],
			),
		));

		QuestAffiliationLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $college['record']['id'],
			),
		));

		$params = array(
			'college' => $college['params']['name'],
			'college_id' => $college['record']['id'],
			'name' => $quest['params']['objective'],
			'description' => $quest['params']['description'],
			'begin_date' => $quest_date_range['params']['begin_date'],
			'end_date' => $quest_date_range['params']['end_date'],
		);
		
		$record = array(
			'id' => $quest['record']['id'],
			'college' => $college['record']['name'],
			'college_id' => $college['record']['id'],
			'name' => $quest['record']['objective'],
			'description' => $quest['record']['description'],
			'begin_date' => $quest_date_range['record']['begin_date'],
			'end_date' => $quest_date_range['record']['end_date'],
		);

		return array('record' => $record, 'params' => $params);

	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		QuestDateRangeDAOSetup::CleanUp();
		CollegeDAOSetup::CleanUp();

	}
	
}
