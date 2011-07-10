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
				'type_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'type of the timestamp, e.g. begin, end, checkpoint, appointment.',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'type_timestamp_relation' => array('type_id', 'timestamp'),
				'timestamp' => array('timestamp'),
			),
		);
	}
}
