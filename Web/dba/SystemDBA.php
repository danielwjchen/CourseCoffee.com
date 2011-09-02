<?php
/**
 * @file
 * Define tables to administer system tasks
 */
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
					'script' => array(
						'type' => 'text',
						'description' => 'the requested sql script',
					),
					'timestamp' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the timestamp when the DBA request is performed',
					),
				),
				'primary' => array('id', 'request'),
			),
			'crypto' => array(
				'description' => 'keep a record of salt user',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'description' => 'the primary key',
					),
					'salt' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => ' a unique string to be used as the "salt" for encryption',
					),
					'timestamp' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the timestamp when the DBA request is performed',
					),
				),
				'primary' => array('id'),
			),
			'cache' => array(
				'description' => 'implement database based caching system',
				'column' => array(
					'key' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'cache key',
					),
					'value' => array(
						'type' => 'text',
						'size' => 'medium',
						'not null' => TRUE,
						'value' => 'cache value',
					),
					'created' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is created',
					),
					'expire' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is expire',
					),
				),
				'primary' => array('key'),
				'index' => array(
					'expire' => array('expire'),
				),
			),
			'file_cache' => array(
				'description' => 'implement a file based caching system',
				'column' => array(
					'key' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'cache key',
					),
					'created' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is created',
					),
					'expire' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is expire',
					),
				),
				'primary' => array('key'),
				'index' => array(
					'expire' => array('expire'),
				),
			),
			'autoloader' => array(
				'description' => 'register a class and the file path',
				'column' => array(
					'class' => array(
						'type' => 'varchar',
						'length' => '64',
						'not null' => TRUE,
						'description' => 'name of the class',
					),
					'path' => array(
						'type' => 'varchar',
						'length' => '256',
						'not null' => TRUE,
						'description' => 'file path',
					),
				),
				'unique' => array(
					'path' => array('path'),
					'class' => array('class'),
				),
			),
/*
			'scheduler' => array(
				'description' => 'schedule jobs',
				'column' => array(
					'callback' => array(
						'type' => 'varchar',
						'length' => '64',
						'not null' => TRUE,
						'description' => 'name of the job class to be called.',
					),
					'performed' => array(
						'type' => 'int',
						'not null' => TRUE,
						'description' => 'the timestamp when the job is performed',
					),
				),
				'primary' => array(
					'callback' => array('callback'),
				),
				'index' => array(
					'performed' => array('performed')
				),
			),
*/
			'router' => array(
				'description' => 'Map path to controller',
				'column' => array(
					'path' => array(
						'type' => 'varchar',
						'length' => '64',
						'not null' => TRUE,
						'description' => 'the path that is mapped to a controller',
					),
					'controller' => array(
						'type' => 'varchar',
						'length' => '64',
						'not null' => TRUE,
						'description' => 'name of the controller class',
					),
					'action' => array(
						'type' => 'varchar',
						'length' => '64',
						'not null' => TRUE,
						'description' => 'name of the method if the controlelr class that will carry out the action',
					),
				),
				'unique' => array(
					'path' => array('path'),
				),
				'index' => array(
					'controller' => array('controller')
				),
			),
		);
	}
}
