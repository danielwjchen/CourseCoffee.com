<?php
/**
 * @file
 * Define schema to store static information on the website, a.k.a. Tale
 */
class TaleSchema extends DefaultSchema implements SchemaInterface {

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
		return array(
			'tale' => array(
				'description' => 'define tale',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies tale',
					),
					'vid' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 1,
						'description' => 'the primary key that identifies tale version',
					),
					'user_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies the creating user',
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
					'title' => array(
						'type' => 'char',
						'length' => 255,
						'not null' => TRUE,
						'description' => 'a name used to identified tale',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'user_id' => array('user_id'),
					'vid' => array('vid'),
				),
				'unique' => array(
					'title' => array('title'),
				),
			),
			'tale_version' => array(
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies tale version',
					),
					'story' => array(
						'type' => 'text',
						'not null' => TRUE,
						'description' => 'a detailed description of the tale',
					),
				),
				'primary' => array('id'),
			),
		);
	}
}
