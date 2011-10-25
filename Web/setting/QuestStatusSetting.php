<?php
/**
 * Preset values in date_type table
 */
class QuestStatusSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by QuestStatusTypeSetting
	 */
	const DELETED = 'removed';
	const PENDING  = 'new';
	const APPROVED = 'approved';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'quest_status';
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
				'value'  => self::DELETED,
			),
			array(
				'field' => 'name',
				'value'  => self::PENDING,
			),
			array(
				'field' => 'name',
				'value'  => self::APPROVED,
			),
		);
	}

}
