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
		);
	}
}
