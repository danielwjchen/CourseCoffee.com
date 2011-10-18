<?php
/**
 * @file
 * Create table schemas to store information of a college institution
 */
class InstitutionSchema extends DefaultSchema implements SchemaInterface {

	/**
	 * Implement SchemaInterface::getDefinition()
	 */
	public function getDefinition() {
		return array(
			'institution' => array(
				'description' => 'Creates a table to deal with institutions',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'size' => 'normal',
						'description' =>'The primary key of the insitution table',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'The name of institution, e.g. Michigan State University, University of Oregon, University of Michigan, etc.',
					),
					'uri' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'A URI safe version of the name of institution, e.g. michigan-state-university.',
					),
					'domain' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'The sub domain that will be used to identify the insitution in the system, e.g. msu (Michigan State University), umich (University of Michigan).',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'uri' => array('uri'),
					'name' => array('name'),
					'domain' => array('domain'),
				),
			),
			'institution_year' => array(
				'description' => 'This table keeps the record of the year(s) a institution is offered.',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => 'TRUE',
						'not null' => 'TRUE',
						'size' => 'normal',
						'description' => 'This is the primary key',
					),
					'period' => array(
						'type' => 'int',
						'size' => 'small',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The year which this institution offers classes.',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'period' => array('period'),
				),
			),
			'institution_year_linkage' => array(
				'description' => 'This table creates a linkage among institutions and the year periods.',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => 'TRUE',
						'not null' => 'TRUE',
						'size' => 'normal',
						'description' => 'This is the primary key',
					),
					'institution_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The primary key that identifies a institution record.',
					),
					'year_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The primary key that identifies a year record.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'institution_id' => array('institution_id'),
					'year_id' => array('year_id'),
				),
			),
			'institution_term' => array(
				'description' => 'This table keeps the record of the term(s) a institution offers courses.',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => 'TRUE',
						'not null' => 'TRUE',
						'size' => 'normal',
						'description' => 'This is the primary key',
					),
					'institution_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The primary key that identifies a institution record.',
					),
					'year_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'This is the key that links the term with its offered year',
					),
					'name' => array(
						'type' => 'char',
						'length' => 24,
						'not null' => TRUE,
						'description' => 'The name of the term, e.g. Spring, Fall, etc.',
					),
					'begin_timestamp' => array(
						'type' => 'int',
						'unsigned' => 'TRUE',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The timestamp of the date which this term starts.',
					),
					'end_timestamp' => array(
						'type' => 'int',
						'unsigned' => 'TRUE',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The timestamp of the date which this term ends.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'institution_id' => array('institution_id'),
					'year_id' => array('year_id'),
				),
			),
		);
	}
}
