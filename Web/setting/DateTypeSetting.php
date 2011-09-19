<?php
/**
 * Preset values in date_type table
 */
class DateTypeSetting extends Setting implements SettingInterface {

	/**
	 * @defgroup default values
	 * @{
	 * Default values defined by DateTypeSetting
	 */
	const END_DATE   = 'end_date';
	const BEGIN_DATE = 'begin_date';
	const CHECKPOINT = 'checkpoint';
	/**
	 * @} End of default values
	 */
	
	/**
	 * Implement SettingInterface::defineTable()
	 */
	public function defineTable() {
		return 'date_type';
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
				'value'  => self::END_DATE,
			),
			array(
				'field' => 'name',
				'value'  => self::BEGIN_DATE,
			),
			array(
				'field' => 'name',
				'value'  => self::CHECKPOINT,
			),
		);
	}

}
