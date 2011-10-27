<?php
/**
 * Preset values in user_role table
 */
class UserRoleSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by UserRoleSetting
	 */
	const SUPER_ADMIN = 'super_admin';
	const ADMIN       = 'admin';
	const MODERATOR   = 'moderator';
	const MEMBER      = 'member';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'user_role';
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
				'value'  => self::SUPER_ADMIN,
			),
			array(
				'field' => 'name',
				'value'  => self::ADMIN,
			),
			array(
				'field' => 'name',
				'value'  => self::MODERATOR,
			),
			array(
				'field' => 'name',
				'value'  => self::MEMBER,
			),
		);
	}

}
