<?php
/**
 * @file
 * Create table schema for Quest and populate default types
 */

class QuestSchema extends InstitutionSchema implements SchemaInterface{

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
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
					'status_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a quest status',
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
			'date' => array(
				'description' => 'define a date',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a date',
					),
					'timestamp' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the UNIX timestamp of the date',
					),
					'type_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'type of the timestamp, e.g. begin, end, checkpoint, appointment.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'type_timestamp_relation' => array('type_id', 'timestamp'),
					'type_id' => array('type_id'),
					'timestamp' => array('timestamp'),
				),
			),
			'date_type' => array(
				'description' => 'define possible date type',
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
			'quest_status' => array(
				'description' => 'define possible quest status',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a attribute status',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the name of the status',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
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
			'quest_location_linkage' => array(
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
			),
			'quest_section_linkage' => array(
				'description' => 'provide a many-to-many mapping among quests and classes',
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
			'quest_user_linkage' => array(
				'description' => 'create many-to-many mapping among users and quests',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'quest_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a quest',
					),
					'user_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'parent_child_relation' => array('quest_id', 'user_id'),
					'quest_id' => array('quest_id'),
					'user_id' => array('user_id'),
				),
			),
		);
	}
}
