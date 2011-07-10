<?php

class ItemTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'item_type',
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
		);
	}
}
