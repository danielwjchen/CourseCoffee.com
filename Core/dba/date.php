<?php

class DateDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'date',
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
			),
			'primary' => array('id'),
			'index' => array(
				'timestamp' => array('timestamp'),
			),
		);
	}
}
