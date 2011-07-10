<?php

class OAuth2DBA implements DBAInterface{
	
	/**
	 * Implement DBAInterface::schema()
	 */
	public function schema() {
		return array(
			'client' => array(
				'description' => 'Store OAuth2 clients.',
				'column' => array(
					'id' => array(
						'type' => 'varchar',
						'length' => '20',
						'not null' => TRUE,
						'description' => 'the primary key that identifies a client',
					),
					'secret' => array(
						'type' => 'varchar',
						'length' => '20',
						'not null' => TRUE,
						'description' => 'the secret that helps to identify a client',
					),
					'redirect_uri' => array(
						'type' => 'varchar',
						'length' => '200',
						'not null' => TRUE,
						'description' => 'the URI for redirect after the client is identified',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'secret' => array('secret')
				),
			),
			'code' => array(
				'description' => 'Store OAuth2 code issued.',
				'column' => array(
					'id' => array(
						'type' => 'varchar',
						'length' => '40',
						'not null' => TRUE,
						'description' => 'the primary key that identifies a code',
					),
					'client_id' => array(
						'type' => 'varchar',
						'length' => '20',
						'not null' => TRUE,
						'description' => 'the primary key that identifies a client',
					),
					'redirect_uri' => array(
						'type' => 'varchar',
						'length' => '200',
						'not null' => TRUE,
						'description' => 'the URI for redirect after the code is issued',
					),
					'expires' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the timestamp of which the code expires',
					),
					'scope' => array(
						'type' => 'varchar',
						'length' => '250',
						'description' => 'the scope of permission which this code is granted access',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'client_id' => array('client_id')
				),
			),
			'token' => array(
				'description' => 'Store OAuth2 token issued.',
				'column' => array(
					'id' => array(
						'type' => 'varchar',
						'length' => '40',
						'not null' => TRUE,
						'description' => 'the primary key that identifies a token',
					),
					'client_id' => array(
						'type' => 'varchar',
						'length' => '20',
						'not null' => TRUE,
						'description' => 'the primary key that identifies a client',
					),
					'expires' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the timestamp of which the token expires',
					),
					'scope' => array(
						'type' => 'varchar',
						'length' => '250',
						'description' => 'the scope of permission which this token is granted access',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'client_id' => array('client_id')
				),
			),
		);
	}
}
