<?php

class QuestMessageLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_message_linkage',
			'description' => 'provide a many-to-many mapping among quests and messages',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a linkage',
				),
				'quest_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a quest',
				),
				'message_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a message',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_message_relation' => array('quest_id', 'message_id'),
				'quest_id' => array('quest_id'),
				'message_id' => array('message_id'),
			),
		);
	}
}
