<?php
/**
 * Preset values in file_type table
 */
class FileTypeSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by FileTypeSetting
	 */
	const PROFILE_IMAGE = 'profile_image';
	const SYLLABUS      = 'syllabus';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'file_type';
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
				'value'  => self::PROFILE_IMAGE,
			),
			array(
				'field' => 'name',
				'value'  => self::SYLLABUS,
			),
		);
	}

}
