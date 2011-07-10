<?php

require_once TEST_CORE_DAO_PATH . '/setups/CollegeSubjectDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAttributeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestLinkageDAOSetup.php';

class CollegeCourseDAOSetup extends DAOSetup implements DAOSetupInterface{

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
			self::truncateTable('quest_linkage');

		}

		$college_subject = CollegeSubjectDAOSetup::Prepare(array(
			'no_truncate' => true
		));

		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_course'
			),
		));

		$quest_attribute = QuestAttributeDAOSetup::Prepare(array(
			'no_truncate' => true,
			'id' => $quest['record']['id'],
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_course_num',
			),
		));

		$linkage = QuestAffiliationLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $college_subject['record']['college_id'],
			),
		));

		/*
		QuestLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $college_subject['record']['id'],
				'child_id' => $quest['record']['id'],
			),
		));
		*/

		$params = array(
			'college' => $college_subject['record']['college'],
			'subject' => $college_subject['params']['subject'],
			'title' => $quest['params']['objective'],
			'description' => $quest['params']['description'],
			'num' => $quest_attribute['params']['value'],
		);

		$record = array(
			'college' => $college_subject['record']['college'],
			'college_id' => $college_subject['record']['college_id'],
			'subject' => $college_subject['record']['subject'],
			'subject_id' => $college_subject['record']['id'],
			'id' => $quest['record']['id'],
			'title' => $quest['record']['objective'],
			'description' => $quest['record']['description'],
			'num' => $quest_attribute['record']['value'],
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		CollegeSubjectDAOSetup::CleanUp();
		QuestDAOSetup::CleanUp();
		QuestAttributeDAOSetup::CleanUp();
		QuestAffiliationLinkageDAOSetup::CleanUp();
	}

}
