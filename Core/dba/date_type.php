<?php

class DateTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'date_type',
			'description' => 'define possible date type',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a attribute type',
				),
				'name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'the name of the type',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'name' => array('name'),
			),
		);
	}
}
