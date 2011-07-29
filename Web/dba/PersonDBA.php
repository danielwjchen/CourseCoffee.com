<?php

class PersonDBA implements DBAInterface{
	public function schema() {
		return array(
			'person' => array(
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
			),
			'person_type' => array(
				'description' => 'define possible person type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a attribute type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the name of the type',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
			'person_email' => array(
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
					'affiliation_id' => array('affiliation_id'),
				),
			),
		);
	}
}
