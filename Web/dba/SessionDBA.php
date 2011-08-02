<?php
/**
 * @file
 * Create table schema for Session and populate default types
 */
class SessionDBA implements DBAInterface{

	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO session_type (name) VALUES 
				('" . SessionType::ARRANGED . "'), 
				('" . SessionType::LAB . "'),
				('" . SessionType::LECTURE . "'),
				('" . SessionType::ONLINE . "'),
				('" . SessionType::SEMINAR . "'),
				('" . SessionType::RECITATION . "')"
		);
	}


	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'session' => array(
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'The primary key',
					),
					'section_id' => array(
						'type' => 'int',
						'size' => 'normal',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The section which this session belongs.',
					),
					'type_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The primary key that identifies a session type.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'section_id' => array('section_id'),
					'type_id' => array('type_id'),
				),
				'session_type' => array(
					'description' => 'Creates a table to store possible session types',
					'column' => array(
						'id' => array(
							'type' => 'serial',
							'unsigned' => TRUE,
							'not null' => TRUE,
							'description' => 'The primary key',
						),
						'name' => array(
							'type' => 'char',
							'not null' => TRUE,
							'length' => 12,
							'description' => 'The session type could be LECTURE, ONLINE, LAB, RECITATION, or SEMINAR.',
						),
					),
					'primary' => array('id'),
					'unique' => array(
						'name' => array('name'),
					),
				),
			),
		);
	}
}
