<?php

class QuestDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest',
			'description' => 'create a table to hold information about a quest.',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a quest',
				),
        'user_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies the creating user',
				),
				'type_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a quest type',
				),
				'objective' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'the objective of the quest',
				),
				'description' => array(
					'type' => 'text',
					'not null' => TRUE,
					'description' => 'a detailed description of the context of a quest',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
      'index' => array(
        'user_id' => array('user_id'),
        'objective' => array('objective'),
        'user_objective_relation' => array('user_id', 'objective'),
      )
		);
	}
}
