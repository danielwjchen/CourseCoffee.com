<?php

class MerchantItemLinkageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'merchant_item_linkage',
			'description' => 'provide a many-to-many mapping among merchants and items',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a linkage',
				),
				'merchant_id' => array(
					'type' => 'int',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the primary key that identifies a merchant',
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
				'merchant_item_relation' => array(
					'merchant_id',
					'item_id',
				),
				'merchant_id' => array('merchant_id'),
				'item_id' => array('item_id'),
			),
		);
	}
}
