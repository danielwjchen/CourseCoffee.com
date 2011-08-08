<?php
/**
 * @file
 * Create table schema for Book and populate default types
 */

class BookDBA implements DBAInterface{

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
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
						'not null' => TRUE,
						'description' => 'isbn of the book',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'isbn' => array('isbn'),
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
