<?php

class QuestDateLinkageDBA implements DBAInterface{
	public function schema() {
		return array(
			'quest_date_linkage' => array(
				'description' => 'provide a many-to-many mapping among quests and dates',
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
					'date_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a date',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'quest_date_relation' => array('quest_id', 'date_id'),
					'quest_id' => array('quest_id'),
					'date_id' => array('date_id'),
				),
			),
		);
	}
}

