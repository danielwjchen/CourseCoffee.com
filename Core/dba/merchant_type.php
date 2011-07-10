<?php

class MerchantTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'merchant_type',
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
		);
	}
}
