<?php

class AffiliationDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'affiliation',
			'description' => 'define a affiliation',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => "the primary key that identifies an affiliation ",
				),
				'type_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a type of affiliation',
				),
				'name' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'name of the affiliation',
				),
				'url' => array(
					'type' => 'char',
					'length' => 64,
					'description' => 'the URL of the affiliation',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'name' => array('name'),
				'url' => array('url'),
			),
			'index' => array(
				'type_id' => array('type_id'),
			),
		);
	}
}
