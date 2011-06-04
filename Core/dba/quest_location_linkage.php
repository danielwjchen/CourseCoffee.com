<?php

class QuestLocationLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_location_linkage',
			'description' => 'provide a many-to-many mapping among quests and locations',
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
				'location_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a location',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_id' => array('quest_id'),
				'location_id' => array('location_id'),
			),
		);
	}
}
