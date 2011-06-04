<?php

class AchievementDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'achievement',
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
		);
	}
}
