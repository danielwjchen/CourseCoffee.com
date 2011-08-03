<?php
/**
 * @file
 * Define linkages among quest and sections
 */

class QuestSectionLinkageDBA implements DBAInterface{
	public function schema() {
		return array(
			'quest_section_linkage' => array(
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
					'section_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a section',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'quest_section_relation' => array('quest_id', 'section_id'),
					'quest_id' => array('quest_id'),
					'section_id' => array('section_id'),
				),
			),
		);
	}
}
