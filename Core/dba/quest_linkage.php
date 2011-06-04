<?php

class QuestLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_linkage',
			'description' => 'define the relation among quests to create sub-quests (tasks)',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a linkage',
				),
				'parent_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a parent quest',
				),
				'child_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a sub-quest (task)',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'parent_child_relation' => array('parent_id', 'child_id'),
				'parent_id' => array('parent_id'),
				'child_id' => array('child_id'),
			),
		);
	}
}
