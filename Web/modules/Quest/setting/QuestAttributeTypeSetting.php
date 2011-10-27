<?php
/**
 * Preset values in quest_attribute_type table
 */
class QuestAttributeTypeSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by QuestAttributeTypeSetting
	 */
	const STATUS = 'status';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'quest_attribute_type';
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
				'value'  => self::STATUS,
			),
		);
	}

}
