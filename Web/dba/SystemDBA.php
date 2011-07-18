<?php

class SystemDBA implements DBAInterface {
	
	/**
	 * Implement DBAInterface::schema()
	 */
	public function schema() {
		return array(
			'DBA' => array(
				'description' => 'This table manages schemas installed via DBA',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'description' => 'the primary key',
					),
					'request' => array(
						'type' => 'char',
						'length' => 32,
						'not null' => TRUE,
						'description' => 'name of the schema',
					),
					'schema' => array(
						'type' => 'text',
						'not null' => TRUE,
						'description' => 'the schema array encoded in json string',
					),
					'timestamp' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the timestamp when the DBA request is performed',
					),
				),
				'primary' => array('id', 'request'),
			),
		);
	}
}
