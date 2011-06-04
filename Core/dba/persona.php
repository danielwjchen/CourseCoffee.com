<?php

class PersonaDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'persona',
			'description' => 'define a persona',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a persona',
				),
				'name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'name of the persona',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'name' => array('name')
			),
		);
	}
}
