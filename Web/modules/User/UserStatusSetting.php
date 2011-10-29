<?php
/**
 * Preset values in user_status table
 */
class UserStatusSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by UserStatusSetting
	 */
	const NEWLY_CREATED = 'new';
	const CONFIRMED     = 'confirmed';
	const BLOCKED       = 'blocked';
	const DELETED       = 'deleted';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'user_status';
	}

	/**
	 * Implement SettingInterface::defineDB()
	 */
	public function defineDB() {
		return array('default');
	}

	/**
	 * Implement SettingInterface::defineSetting()
	 */
	public function defineSetting() {
		return array(
			array(
				'field' => 'name',
				'value'  => self::NEWLY_CREATED,
			),
			array(
				'field' => 'name',
				'value'  => self::CONFIRMED,
			),
			array(
				'field' => 'name',
				'value'  => self::BLOCKED,
			),
			array(
				'field' => 'name',
				'value'  => self::DELETED,
			),
		);
	}

}
