<?php

class StatisticTypeDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'statistic_type',
			'description' => 'define an statistic type',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => "the primary key that identifies an statistic type",
				),
				'name' => array(
					'type' => 'char',
					'length' => 64,
					'not null' => TRUE,
					'description' => 'the name of the statistic type, e.g. traffic, best_reply, comment.',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'name' => array('name'),
			),
		);
	}
}
