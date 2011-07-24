<?php
/**
 * @file
 * Create table schema for Statistic and populate default types
 */

class AchievementDBA implements DBAInterface{
	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO achievement_type (name) VALUES 
				('" . AchievementType::HIGHEST_KARMA. "'), 
				('" . AchievementType::MOST_COMMENT . "'),
				('" . AchievementType::MOST_BEST_REPLY . "')",
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'achievement' => array(
				'description' => 'define a achievement',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a achievement',
					),
					'type_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'type of the achievement, e.g. personal.',
					),
					'name' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'name of the achievement',
					),
					'metric' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'metric of the achievement',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'type_id' => array('type_id'),
				),
				'index' => array(
					'name' => array('name')
				),
			),
			'achievement_type' => array(
				'description' => 'define a achievement type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a achievement type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'name of the achievement type',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
		);
	}
}
