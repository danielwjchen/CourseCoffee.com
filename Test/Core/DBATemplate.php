<?php
require_once 'bootstrap.php';

class TestDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'test',
			'description' => 'Create a test table',
			'column' => array(
				'nid' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'The primary key',
				),
				'vid' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'The revision number',
				),
				'subject_id' => array(
					'type' => 'int',
					'size' => 'normal',
					'not null' => TRUE,
					'default' => 0,
					'description' => 'The primary key that identifies the subject which this course belongs.',
				),
				'num' => array(
					'type' => 'char',
					'length' => 8,
					'not null' => TRUE,
					'description' => 'The number which this course is assigned',
				),
			),
			'primary' => array('nid', 'vid'),
			'unique' => array(
				'vid' => array('vid')
			),
			'index' => array(
				'nid' => array('nid'),
				'subject_id' => array('subject_id'),
				'num' => array('num'),
			),
		);
	}
}
