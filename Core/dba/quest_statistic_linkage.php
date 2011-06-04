<?php

class QuestStatisticLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_statistic_linkage',
			'description' => 'provide a many-to-many mapping among quests and statistics',
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
				'statistic_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a statistic',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_statistic_relation' => array(
					'quest_id',
					'statistic_id',
				),
				'quest_id' => array('quest_id'),
				'statistic_id' => array('statistic_id'),
			),
		);
	}
}
