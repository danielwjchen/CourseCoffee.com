<?php

class QuestAchievementLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_achievement_linkage',
			'description' => 'provide a many-to-many mapping among quests and achievements',
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
				'achievement_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a achievement',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_achievement_relation' => array('quest_id', 'achievement_id'),
				'quest_id' => array('quest_id'),
				'achievement_id' => array('achievement_id'),
			),
		);
	}
}
