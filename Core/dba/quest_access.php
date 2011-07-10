<?php

class QuestAccessDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_access',
			'description' => "restric a user's access to a quest",
			'column' => array(
				'quest_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a quest',
				),
				'user_id' => array(
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
