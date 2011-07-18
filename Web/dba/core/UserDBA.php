<?php

class UserDBA implements DBAInterface{
	public function schema() {
		return array(
			'user' => array(
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
			),
			'user_quest_linkage' => array(
				'description' => 'create many-to-many mapping among users and quests',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'user_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'quest_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a quest',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'parent_child_relation' => array('user_id', 'quest_id'),
					'user_id' => array('user_id'),
					'quest_id' => array('quest_id'),
				),
			),
		);
	}
}
