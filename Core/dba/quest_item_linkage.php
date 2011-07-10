<?php

class QuestItemLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_item_linkage',
			'description' => 'provide a many-to-many mapping among quests and items',
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
				'item_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a item',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_item_relation' => array('quest_id', 'item_id'),
				'quest_id' => array('quest_id'),
				'item_id' => array('item_id'),
			),
		);
	}
}
