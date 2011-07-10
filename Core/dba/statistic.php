<?php

class StatisticDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'statistic',
			'description' => 'define a statistic',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a statistic',
				),
				'type_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'type of the statistic collected, e.g. visitor count.',
				),
				'data' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'data collected',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'type_data_relation' => array('type_id', 'data'),
			),
		);
	}
}
