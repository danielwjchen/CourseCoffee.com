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
				'quest_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a quest',
				),
				'type_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies an attribute type ',
				),
				'value' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'the value of the attribute',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'quest_id' => array('quest_id'),
				'value' => array('value'),
			),
		);
	}
}
