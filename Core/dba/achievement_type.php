<?php

class AchievementTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'achievement_type',
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
		);
	}
}
