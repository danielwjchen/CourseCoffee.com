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
					'key' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'cache key',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'book_section_relation' => array('book_id', 'section_id'),
					'book_id' => array('book_id'),
					'section_id' => array('section_id'),
				),
			),
			'book_crawler_queue' => array(
				'description' => 'keep track of book lists stored in cache that needed to be refreshed.',
				'column' => array(
					'cache_key' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'cache key',
					),
					'status' => array(
						'type' => 'char',
						'length' => 32,
						'not null' => TRUE,
						'description' => 'a status flag to indicate the state of the queued item, e.g. NEW, STARTED, FINISHED, FAILED.',
					),
					'created' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'UNIX timestamp of the moment when the item is created.',
					),
					'updated' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'UNIX timestamp of the moment when the item is processed.',
					),
				),
				'primary' => array('cache_key'),
				'index' => array(
					'status' => array('status'),
				),
			),
		);

	}

}
