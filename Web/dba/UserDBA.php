<?php
/**
 * @file
 * Create table schema for User and populate default types
 */
class UserDBA implements DBAInterface{

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'user' => array(
				'description' => 'define user',
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
				'unique' => array('account' => array('account')),
				'index' => array('password' => array('password')),
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
			'user_setting' => array(
				'description' => 'store user setting',
				'column' => array(
					'user_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a user',
					),
					'institution_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a institution',
					),
					'year_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a year',
					),
					'term_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a term',
					),
					'created' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is created',
					),
					'updated' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The UNIX timestamp of when quest is updated',
					),
					/**
					 * To-do
					 *
					'language_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a supported language',
					),
					'status_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies user status',
					),
					'role_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies user role',
					),
					*/
					'tou_vid' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a version of terms of use',
					),
				),
				'primary' => array('user_id'),
				'index' => array(
					'institution_id' => array('institution_id'),
					'year_id' => array('year_id'),
					/**
					 * To do
					 *
					'status_id' => array('status_id'),
					'role_id'   => array('role_id'),
					 */
					'term_id' => array('term_id'),
					'tou_vid' => array('tou_vid'),
				),
			),
			'user_status' => array(
				'description' => "define user status",
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a user status',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the signature generated for the user',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
			'user_role' => array(
				'description' => "define user role",
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a user role',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'role name',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
			'user_section_linkage' => array(
				'description' => 'provide a one-to-many mapping among user and section',
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
					'section_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a section ',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_section_relation' => array('user_id', 'section_id'),
					'user_id' => array('user_id'),
					'section_id' => array('section_id'),
				),
			),
			'user_institution_linkage' => array(
				'description' => 'provide a one-to-many mapping among user and institution',
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
					'institution_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a institution ',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_institution_relation' => array('user_id', 'institution_id'),
					'user_id' => array('user_id'),
					'institution_id' => array('institution_id'),
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
						'size' => 'big',
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
		);
	}
}
