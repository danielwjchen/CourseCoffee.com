<?php

require_once TEST_CORE_DAO_PATH . '/setups/CollegeCourseDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAttributeDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestAffiliationLinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestLinkageDAOSetup.php';

class CollegeSectionDAOSetup extends DAOSetup implements DAOSetupInterface{

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

		$college_course = CollegeCourseDAOSetup::Prepare(array(
			'no_truncate' => true
		));

		$quest = QuestDAOSetup::Prepare(array(
			'no_truncate' => true,
			'type' => array(
				'no_truncate' => true,
				'specified' => 'college_section'
			),
		));

		$linkage = QuestAffiliationLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $quest['record']['id'],
				'child_id' => $college_course['record']['college_id'],
			),
		));

		/*
		QuestLinkageDAOSetup::Prepare(array(
			'no_truncate' => true,
			'specified' => array(
				'parent_id' => $college_course['record']['id'],
				'child_id' => $quest['record']['id'],
			),
		));
		*/

		$params = array(
			'college' => $college_course['record']['college'],
			'subject' => $college_course['params']['subject'],
			'course' => $college_course['params']['title'],
			'section' => $quest['params']['objective'],
			'description' => $quest['params']['description'],
		);

		$record = array(
			'college' => $college_course['record']['college'],
			'college_id' => $college_course['record']['college_id'],
			'subject' => $college_course['record']['subject'],
			'subject_id' => $college_course['record']['subject_id'],
			'course' => $college_course['params']['title'],
			'course_id' => $college_course['record']['id'],
			'id' => $quest['record']['id'],
			'section' => $quest['record']['objective'],
			'description' => $quest['record']['description'],
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
