<?php

class ItemDBA implements DBAInterface{
	public function schema() {
		return array(
			'item' => array(
				'description' => 'define a item',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a item',
					),
					'type_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'not null' => TRUE,
						'default' => 0,
						'description' => 'type of the item, e.g. book.',
					),
					'name' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'name of the item',
					),
				),
				'primary' => array('id'),
				'index' => array(
					'type_id' => array('type_id'),
				),
			),
			'item_type' => array(
				'description' => 'define a item type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a item',
					),
					'name' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'name of the type, e.g. book',
					),
				),
				'primary' => array('id'),
				'unique' => array(
					'name' => array('name'),
				),
			),
			'item_attribute' => array(
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
				'index' => array(
					'item_id' => array('item_id'),
					'type_id' => array('type_id'),
					'value' => array('value'),
				),
			),
			'item_attribute_type' => array(
				'description' => 'define possible types of item attributes',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a attribute type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'the name of the attribute type',
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
