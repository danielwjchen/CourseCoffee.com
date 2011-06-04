<?php

class AffiliationLocationLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'affiliation_location_linkage',
			'description' => 'create many-to-many mapping among affiliations and locations',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a achievement',
				),
				'affiliation_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a affiliation',
				),
				'location_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a location',
				),
			),
			'primary' => array('id'),
			'index' => array(
				'affiliation_location_relation' => array(
					'affiliation_id', 
					'location_id'
				),
				'affiliation_id' => array('affiliation_id'),
				'location_id' => array('location_id'),
			),
		);
	}
}
