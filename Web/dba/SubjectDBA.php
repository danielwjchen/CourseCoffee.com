<?php
/**
 * @file
 * Create table schema for course subject
 */

class SubjectDBA implements DBAInterface{

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'subject' => array(
				'description' => 'Creates a table to store subject of a course',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => "the primary key that identifies an subject ",
					),
					'title' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'title of the subject',
					),
					'abbr' => array(
						'type' => 'char',
						'length' => 64,
						'description' => 'the abbreviation of the subject',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'title_abbr' => array('title', 'abbr'),
				),
				'index' => array(
					'abbr' => array('abbr'),
					'title' => array('title'),
				),
			),
			'subject_term_linkage' => array(
				'description' => 'Create linkage among courses and terms',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'The primary key',
					),
					'term_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'This is the primary key that identifies a term.',
					),
					'subject_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'This is the primary key that identifies a course.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'term_id' => array('term_id'),
					'subject_id' => array('subject_id'),
				),
			),
		);
	}
}
