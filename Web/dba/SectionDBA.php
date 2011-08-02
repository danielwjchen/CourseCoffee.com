<?php
/**
 * @file
 * Create table schema for Section and populate default types
 */
class SectionDBA implements DBAInterface{

	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO section_type (name) VALUES 
				('" . SectionType::HONOR . "'), 
				('" . SectionType::HYBRID . "'),
				('" . SectionType::OFF_CAMPUS . "'),
				('" . SectionType::ON_CAMPUS . "'),
				('" . SectionType::ONLINE . "')"
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'section' => array(
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'The primary key',
					),
					'course_id' => array(
						'type' => 'int',
						'size' => 'normal',
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The course which this section belongs.',
					),
					'type_id' => array(
						'type' => 'int',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'The primary key that identifies a section type.',
					),
					'num' => array(
						'type' => 'char',
						'length' => 12,
						'not null' => TRUE,
						'description' => 'The section num, e.g. 101, 929, 132H, etc.',
					),
					'credit' => array(
						'type' => 'char',
						'length' => 12,
						'not null' => TRUE,
						'description' => 'The section credit, e.g. a specific numeric value or a range of possiblity.',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'course_id' => array('course_id'),
					'type_id' => array('type_id'),
					'num' => array('num'),
				),
				'section_type' => array(
					'description' => 'Creates a table to store possible section types',
					'column' => array(
						'id' => array(
							'type' => 'serial',
							'unsigned' => TRUE,
							'not null' => TRUE,
							'description' => 'The primary key',
						),
						'name' => array(
							'type' => 'char',
							'not null' => TRUE,
							'length' => 12,
							'description' => 'The section type could be HYBRID, ON_CAMPUS, OFF_CAMPUS, ARRANGED, HONOR, or ONLINE.',
						),
					),
					'primary' => array('id'),
					'unique' => array(
						'name' => array('name'),
					),
				),
				'section_item_linkage' => array(
					'description' => 'provide a many-to-many mapping among sections and items',
					'column' => array(
						'id' => array(
							'type' => 'serial',
							'unsigned' => TRUE,
							'not null' => TRUE,
							'description' => 'the primary key that identifies a linkage',
						),
						'section_id' => array(
							'type' => 'int',
							'unsigned' => TRUE,
							'not null' => TRUE,
							'default' => 0,
							'description' => 'the primary key that identifies a section',
						),
						'item_id' => array(
							'type' => 'int',
							'unsigned' => TRUE,
							'not null' => TRUE,
							'default' => 0,
							'description' => 'the primary key that identifies a item',
						),
					),
					'primary' => array('id'),
					'index' => array(
						'section_item_relation' => array(
							'section_id',
							'item_id',
						),
						'section_id' => array('section_id'),
						'item_id' => array('item_id'),
					),
				),
			),
		);
	}
}
