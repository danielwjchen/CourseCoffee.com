<?php
/**
 * @file
 * Create table schemas to store information of a institution term.
 */
class InstitutionTermSchema extends InstitutionSchema implements SchemaInterface {

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
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
			'section' => array(
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'The primary key',
					),
					'course_id' => array(
						'type' => 'int',
						'size' => 'normal',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The course which this section belongs.',
					),
					'num' => array(
						'type' => 'char',
						'length' => 12,
						'not null' => TRUE,
						'description' => 'The section num, e.g. 101, 929, 132H, etc.',
					),
					'credit' => array(
						'type' => 'char',
						'length' => 12,
						'not null' => TRUE,
						'description' => 'The section credit, e.g. a specific numeric value or a range of possiblity.',
					),
					'syllabus_status' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'a status flag, e.g. HAS_SYLLABUS.',
					),
					'syllabus_id' => array(
						'type' => 'int',
						'size' => 'normal',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'Syllabus id.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'course_id' => array('course_id'),
					'num' => array('num'),
					'syllabus_id' => array('syllabus_id'),
				),
			),
			'user_section_linkage' => array(
				'description' => 'provide a one-to-many mapping among user and section',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'section_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a section ',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_section_relation' => array('user_id', 'section_id'),
					'user_id' => array('user_id'),
					'section_id' => array('section_id'),
				),
			),
			'book' => array(
				'description' => 'define a book',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a book',
					),
					'isbn' => array(
						'type' => 'char',
						'length' => 128,
						'description' => 'isbn of the book',
					),
					'title' => array(
						'type' => 'char',
						'length' => 255,
						'description' => 'book title',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'isbn' => array('isbn'),
				),
				'index' => array(
					'title' => array('title'),
				),
			),
			'book_section_linkage' => array(
				'description' => 'provide a many-to-many mapping among books and sections',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'book_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a book',
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
					'book_section_relation' => array('book_id', 'section_id'),
					'book_id' => array('book_id'),
					'section_id' => array('section_id'),
				),
			),
		);
	}
}
