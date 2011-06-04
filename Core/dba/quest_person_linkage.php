<?php

class QuestPersonLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_person_linkage',
			'description' => 'provide a many-to-many mapping among quests and people',
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
				'person_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a person',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_person_relation' => array('quest_id', 'person_id'),
				'quest_id' => array('quest_id'),
				'person_id' => array('person_id'),
			),
		);
	}
}
