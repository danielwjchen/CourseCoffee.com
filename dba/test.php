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
		);
	}
}
