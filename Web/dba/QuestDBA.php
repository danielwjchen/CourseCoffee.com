<?php
/**
 * @file
 * Create table schema for Quest and populate default types
 */

class QuestDBA implements DBAInterface{

	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO quest_type (name) VALUES 
				('" . QuestType::TASK . "'), 
				('" . QuestType::COLLEGE_EXAM . "'),
				('" . QuestType::COLLEGE_READING . "'),
				('" . QuestType::COLLEGE_LAB . "'),
				('" . QuestType::COLLEGE_ESSAY . "'),
				('" . QuestType::COLLEGE_QUIZ . "')",
			"INSERT INTO quest_attribute_type (name) VALUES 
				('" . QuestAttributeType::STATUS. "')"
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'quest' => array(
				'description' => 'create a table to hold information about a quest.',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a quest',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies the creating user',
					),
					'type_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a quest type',
					),
					'created' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is created',
					),
					'updated' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is updated',
					),
					'objective' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'the objective of the quest',
					),
					'description' => array(
						'type' => 'text',
						'not null' => TRUE,
						'description' => 'a detailed description of the context of a quest',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_id' => array('user_id'),
					'type_id' => array('type_id'),
					'objective' => array('objective'),
					'user_objective_relation' => array('user_id', 'objective'),
				),
			),
			'quest_type' => array(
				'description' => 'define possible quest type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a attribute type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the name of the type',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
			'quest_attribute' => array(
				'description' => 'define the attributes of a quest',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a quest attribute',
					),
					'quest_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a quest',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'type_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies an attribute type ',
					),
					'value' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'the value of the attribute',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_id' => array('user_id'),
					'quest_id' => array('quest_id'),
					'type_id' => array('type_id'),
					'value' => array('value'),
				),
			),
			'quest_attribute_type' => array(
			'description' => 'define possible types of quest attributes',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a attribute type',
				),
				'name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'the name of the attribute type',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'name' => array('name'),
			),
		),
		);
	}
}
