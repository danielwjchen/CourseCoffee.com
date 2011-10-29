<?php
/**
 * Preset values in location_type table
 */
class LocationTypeSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by LocationTypeSetting
	 */
	const CAMPUS   = 'campus';
	const BUILDING = 'building';
	const CITY     = 'city';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'location_type';
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
				'value'  => self::CAMPUS,
			),
			array(
				'field' => 'name',
				'value'  => self::BUILDING,
			),
			array(
				'field' => 'name',
				'value'  => self::CITY,
			),
		);
	}

}
