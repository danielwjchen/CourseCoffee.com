<?php
/**
 * @file
 * Create table schema for Course and populate default types
 */
class CourseDBA implements DBAInterface{

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'course' => array(
				'description' => 'create a table to hold information about a college course.',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a quest',
					),
					'subject_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a academic subject in college',
					),
					'title' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'course title',
					),
					'num' => array(
						'type' => 'char',
						'length' => 32,
						'not null' => TRUE,
						'description' => 'course number',
					),
					'description' => array(
						'type' => 'text',
						'not null' => TRUE,
						'description' => 'a detailed description of the context of a quest',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'subject_id' => array('subject_id'),
					'title' => array('title'),
					'num' => array('num'),
				),
			),
		);
	}
}
