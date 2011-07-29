<?php
/**
 * @file
 * Create table schema for Affiliation and populate default types
 */

class AffiliationDBA implements DBAInterface{

	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO affiliation_type (name) VALUES 
				('" . AffiliationType::COLLEGE . "'), 
				('" . AffiliationType::STUDY_GROUP . "'),
				('" . AffiliationType::COLLEGE_FRATERNITY . "'),
				('" . AffiliationType::COLLEGE_SORORITY . "')",
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'affiliation' => array(
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
			),
			'affiliation_type' => array(
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
						'description' => 'the name of the affiliation type, e.g. student group, college, fraternity.',
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
