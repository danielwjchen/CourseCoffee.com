<?php

class test_DBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'test',
			'column' => array(
				'id' => array(
					'type' => 'int',
				),
				'col_1' => array(
					'type' => 'char',
					'size' => '23',
				),
			),
		'key' => array(
				'primary' => array('nid', 'vid'),
				'unique' => array(
					'vid' => array('vid')
				),
				'index' => array(
					'nid' => array('nid'),
					'subject_id' => array('subject_id'),
					'num' => array('num'),
				),
			),
		);
	}
}
