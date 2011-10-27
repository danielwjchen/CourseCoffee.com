<?php
/**
 * @file
 * Define tables to keep track of files
 */
class FileSchema extends DefaultSchema implements SchemaInterface{

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
		return array(
			'file' => array(
				'description' => 'keep track of uploaded files',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'description' => 'the primary key',
					),
					'user_id' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the primary key that identifies the user that uploaded the file',
					),
					'type_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a type of file',
					),
					'name' => array(
						'type' => 'varchar',
						'length' => '255',
						'not null' => TRUE,
						'description' => 'name of the file',
					),
					'path' => array(
						'type' => 'varchar',
						'length' => '255',
						'not null' => TRUE,
						'description' => 'file path',
					),
					'mime' => array(
						'type' => 'varchar',
						'length' => '255',
						'not null' => TRUE,
						'description' => 'file mime type',
					),
					'size' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'file size',
					),
					'timestamp' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the timestamp when the file is uploaded',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'path' => array('path'),
				),
				'index' => array(
					'user_id' => array('user_id'),
				),
			),
			'file_type' => array(
				'description' => 'define an file type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => "the primary key that identifies an file type",
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the name of the file type, e.g. profile_image, syllabus.',
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
