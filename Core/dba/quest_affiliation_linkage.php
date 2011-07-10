<?php

class QuestAffiliationLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_affiliation_linkage',
			'description' => 'provide a many-to-many mapping among quests and affiliations',
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
				'affiliation_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a affiliation',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'quest_affiliation_relation' => array(
					'quest_id',
					'affiliation_id',
				),
				'quest_id' => array('quest_id'),
				'affiliation_id' => array('affiliation_id'),
			),
		);
	}
}
