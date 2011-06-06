<?php

class QuestAttributeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_attribute',
			'description' => 'define the attributes of a quest',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a quest attribute',
				),
				'type_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a quest',
				),
				'value' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'the value of the attribute',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_id' => array('quest_id'),
				'value' => array('value'),
			),
		);
	}
}
