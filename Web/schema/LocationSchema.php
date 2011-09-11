<?php
/**
 * @file
 * Create table schema for Location and populate default types
 */

class LocationSchema extends InstitutionSchema implements SchemaInterface {

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
		return array(
			'location' => array(
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
						'description' => 'type of the location, e.g. campus, building, city.',
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
			),
			'location_type' => array(
				'description' => 'define a location type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a location type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'name of the location type',
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
