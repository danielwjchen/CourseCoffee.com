<?php

class MerchantDBA implements DBAInterface{
	public function schema() {
		return array(
			'merchant' => array(
				'description' => 'define a merchant',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => "the primary key that identifies an merchant ",
					),
					'type_id' => array(
						'type' => 'int',
						'signed' => TRUE,
						'default' => 0,
						'description' => 'the primary key that identifies a type of merchant',
					),
					'name' => array(
						'type' => 'char',
						'length' => 128,
						'not null' => TRUE,
						'description' => 'name of the merchant',
					),
					'url' => array(
						'type' => 'char',
						'length' => 64,
						'description' => 'the URL of the merchant',
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
			'merchant_type' => array(
				'description' => 'define a merchant type',
				'column' => array(
					'id' => array(
						'type' => 'serial',
						'unsigned' => TRUE,
						'not null' => TRUE,
						'description' => 'the primary key that identifies a merchant type',
					),
					'name' => array(
						'type' => 'char',
						'length' => 64,
						'not null' => TRUE,
						'description' => 'name of the merchant type',
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
