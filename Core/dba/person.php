<?php

class PersonDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'person',
			'description' => 'define a person',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a person',
				),
				'user_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => "the person's user id",
				),
				'first_name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'first name of the person',
				),
				'last_name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'last name of the person',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'first_name' => array('first_name'),
				'last_name' => array('last_name')
			),
		);
	}
}
