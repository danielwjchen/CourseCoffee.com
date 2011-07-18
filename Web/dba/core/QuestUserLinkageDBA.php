<?php

class QuestUserLinkageDBA implements DBAInterface{
	public function schema() {
		return array(
			'quest_user_linkage' => array(
				'description' => 'create many-to-many mapping among users and quests',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
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
						'description' => 'the primary key that identifies a user',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'parent_child_relation' => array('quest_id', 'user_id'),
					'quest_id' => array('quest_id'),
					'user_id' => array('user_id'),
				),
			),
		);
	}
}
