<?php
/**
 * Preset values in quest_type table
 */
class QuestTypeSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by QuestTypeSetting
	 */
	const TASK            = 'task';
	const COLLEGE_EXAM    = 'college_exam';
	const COLLEGE_READING = 'college_reading';
	const COLLEGE_LAB     = 'college_lab';
	const COLLEGE_ESSAY   = 'college_essay';
	const COLLEGE_QUIZ    = 'college_quiz';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'quest_type';
	}

	/**
	 * Implement SettingInterface::defineDB()
	 */
	public function defineDB() {
		return array('institution');
	}

	/**
	 * Implement SettingInterface::defineSetting()
	 */
	public function defineSetting() {
		return array(
			array(
				'field' => 'name',
				'value'  => self::TASK,
			),
			array(
				'field' => 'name',
				'value'  => self::COLLEGE_EXAM,
			),
			array(
				'field' => 'name',
				'value'  => self::COLLEGE_READING,
			),
			array(
				'field' => 'name',
				'value'  => self::COLLEGE_LAB,
			),
			array(
				'field' => 'name',
				'value'  => self::COLLEGE_ESSAY,
			),
			array(
				'field' => 'name',
				'value'  => self::COLLEGE_QUIZ,
			),
		);
	}

}
