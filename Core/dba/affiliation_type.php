<?php

class AffiliationTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'affiliation_type',
			'description' => 'define an affiliation type',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => "the primary key that identifies an affiliation type",
				),
				'name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'the name of the affiliation type, e.g. student group, colleg, fraternity.',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'name' => array('name'),
			),
		);
	}
}
