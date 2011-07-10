<?php

require_once TEST_CORE_DAO_PATH . '/setups/CollegeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAttributeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';

class CollegeSubjectDAOSetup extends DAOSetup implements DAOSetupInterface{

	/**
	 * Override DAOSetup::Prepare().
	 */
  public static function Prepare(array $params = array('random' => true)) {
		if (!isset($params['no_truncate'])) {
			self::truncateTable('quest');
			self::truncateTable('quest_type');
			self::truncateTable('quest_attribute');
			self::truncateTable('quest_attribute_type');
			self::truncateTable('quest_affiliation_linkage');

		}

		$college = CollegeDAOSetup::Prepare();
		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_subject'
			),
		));

		$quest_attribute = QuestAttributeDAOSetup::Prepare(array(
			'no_truncate' => true,
			'id' => $quest['record']['id'],
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_subject_abbr',
			),
		));

		$linkage = QuestAffiliationLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $college['record']['id'],
			),
		));

		$params = array(
			'college' => $college['params']['name'],
			'college_id' => $college['record']['id'],
			'subject' => $quest['params']['objective'],
			'description' => $quest['params']['description'],
			'abbr' => $quest_attribute['params']['value'],
		);

		$record = array(
			'id' => $quest['record']['id'],
			'college' => $college['record']['name'],
			'college_id' => $college['record']['id'],
			'subject' => $quest['record']['objective'],
			'description' => $quest['record']['description'],
			'abbr' => $quest_attribute['record']['value'],
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		CollegeDAOSetup::CleanUp();
		QuestDAOSetup::CleanUp();
		QuestAttributeDAOSetup::CleanUp();
		QuestAffiliationLinkageDAOSetup::CleanUp();
	}

}
