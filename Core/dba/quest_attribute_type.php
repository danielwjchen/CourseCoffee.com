<?php

class QuestAttributeTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'quest_attribute_type',
			'description' => 'define possible types of quest attributes',
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
		);
	}
}
