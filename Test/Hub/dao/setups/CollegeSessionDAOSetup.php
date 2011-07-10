<?php

require_once TEST_CORE_DAO_PATH . '/setups/CollegeSectionDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAttributeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestLinkageDAOSetup.php';

class CollegeSessionDAOSetup extends DAOSetup implements DAOSetupInterface{

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

		$college_section = CollegeSectionDAOSetup::Prepare(array(
			'no_truncate' => true
		));

		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_session'
			),
		));

		$quest_attribute = QuestAttributeDAOSetup::Prepare(array(
			'no_truncate' => true,
			'id' => $quest['record']['id'],
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_session_type',
			),
		));

		$linkage = QuestAffiliationLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $college_section['record']['college_id'],
			),
		));

		/*
		QuestLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $college_section['record']['id'],
				'child_id' => $quest['record']['id'],
			),
		));
		*/

		$params = array(
			'college' => $college_section['record']['college'],
			'subject' => $college_section['params']['subject'],
			'course' => $college_section['params']['course'],
			'section' => $college_section['params']['section'],
			'session' => $quest['params']['objective'],
			'description' => $quest['params']['description'],
			'type' => $quest_attribute['params']['value'],
		);

		$record = array(
			'college' => $college_section['record']['college'],
			'college_id' => $college_section['record']['college_id'],
			'subject' => $college_section['record']['subject'],
			'subject_id' => $college_section['record']['subject_id'],
			'course' => $college_section['params']['course'],
			'course_id' => $college_section['record']['course_id'],
			'section' => $college_section['record']['section'],
			'section_id' => $college_section['record']['id'],
			'id' => $quest['record']['id'],
			'session' => $quest['record']['objective'],
			'description' => $quest['record']['description'],
			'type' => $quest_attribute['record']['value'],
		);

		return array('record' => $record, 'params' => $params);
	}

	/**
	 * Overrid DAOSetup::CleanUp().
	 */
	static public function CleanUp() {
		CollegeCourseDAOSetup::CleanUp();
		QuestDAOSetup::CleanUp();
		QuestAttributeDAOSetup::CleanUp();
		QuestAffiliationLinkageDAOSetup::CleanUp();
	}

}
