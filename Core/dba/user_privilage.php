<?php

class UserPrivilageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'user_privilage',
			'description' => "define a user's privilage",
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => "the primary key that identifies a person's email",
				),
				'user_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a user',
				),
				'action' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'action granted to the user',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'user_id' => array('user_id'),
				'action' => array('action'),
			),
		);
	}
}
