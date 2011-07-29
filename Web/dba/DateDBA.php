<?php
/**
 * @file
 * Create table schema for date and populate default types
 */

class DateDBA implements DBAInterface{

	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO date_type (name) VALUES 
				('" . DateType::END_DATE. "'), 
				('" . DateType::BEGIN_DATE. "'),
				('" . DateType::CHECKPOINT. "')"
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'date' => array(
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
				'index' => array(
					'type_timestamp_relation' => array('type_id', 'timestamp'),
					'type_id' => array('type_id'),
					'timestamp' => array('timestamp'),
				),
			),
			'date_type' => array(
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
			),
		);
	}
}
