<?php

class ItemDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'item',
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
			'unique' => array(
				'type_id' => array('type_id'),
			),
		);
	}
}
