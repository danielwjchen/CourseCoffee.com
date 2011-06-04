<?php

class LocationDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'location',
			'description' => 'define a location',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a location',
				),
				'type_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'type of the achievement, e.g. personal.',
				),
				'longitude' => array(
					'type' => 'float',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the longitude of the location',
				),
				'latitude' => array(
					'type' => 'float',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the latitude of the location',
				),
				'name' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'name of the location',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'longitude' => array('longitude'),
				'latitude' => array('latitude'),
			),
		);
	}
}
