<?php

class PersonEmailDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'person',
			'description' => 'define a person',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => "the primary key that identifies a person's email",
				),
				'person_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a person',
				),
				'affiliation_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a affiliation',
				),
				'email_hash' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'the hash value of a email account',
				),
				'email' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'email address',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'email_hash' => array('email_hash'),
				'email' => array('email'),
			),
			'index' => array(
				'person_id' => array('person_id'),
				'afffiliation_id' => array('afffiliation_id'),
			),
		);
	}
}
