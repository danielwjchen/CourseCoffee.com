<?php

class ItemAttributeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'item_attribute',
			'description' => 'define the attributes of a item',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a item attribute',
				),
				'item_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a item',
				),
				'type_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies an attribute type ',
				),
				'value' => array(
					'type' => 'char',
					'length' => 128,
					'not null' => TRUE,
					'description' => 'the value of the attribute',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'item_id' => array('item_id'),
				'value' => array('value'),
			),
		);
	}
}
