<?php

class UserQuestLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'user_quest_linkage',
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
				'parent_child_relation' => array('parent_id', 'child_id'),
				'user_id' => array('user_id'),
				'quest_id' => array('quest_id'),
			),
		);
	}
}
