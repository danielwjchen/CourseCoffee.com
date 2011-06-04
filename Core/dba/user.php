<?php

class UserDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'user',
			'description' => 'define a user',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a user',
				),
				'account' => array(
					'type' => 'char',
					'length' => 255,
					'not null' => TRUE,
					'description' => 'the account of a user',
				),
				'password' => array(
					'type' => 'char',
					'length' => 255,
					'not null' => TRUE,
					'description' => 'the password of a user',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'account' => array('account'),
				'password' => array('password'),
			),
		);
	}
}
