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
			'autoload' => array(
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
			'user' => array(
				'description' => 'define a user',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a user',
					),
					'account' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'the account of a user',
					),
					'password' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'the password of a user',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'account' => array('account'),
					'password' => array('password'),
				),
			),
			'user_cookie' => array(
				'description' => "store user's cookie signature",
				'column' => array(
					'user_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'signature' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'the signature generated for the user',
					),
				),
				'primary' => array('user_id'),
				'unique' => array(
					'signature' => array('signature'),
				),
			),
			'user_affiliation_linkage' => array(
				'description' => 'provide a one-to-many mapping among user and affiliation',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'affiliation_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a affiliation ',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_affiliation_relation' => array('user_id', 'affiliation_id'),
					'user_id' => array('user_id'),
					'affiliation_id' => array('affiliation_id'),
				),
			),
			'user_facebook_linkage' => array(
				'description' => 'provide a one-to-one mapping among user and facebook account',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a linkage',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'fb_uid' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a facebook account',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_facebook_relation' => array('user_id', 'fb_uid'),
					'user_id' => array('user_id'),
					'fb_uid' => array('fb_uid'),
				),
			),
			'user_session' => array(
				'description' => "store user's session",
				'column' => array(
					'user_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'session' => array(
						'type' => 'text',
						'not null' => TRUE,
						'description' => 'serialized user session',
					),
				),
				'primary' => array('user_id'),
			),
		);
	}
}
