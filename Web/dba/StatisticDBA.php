<?php
/**
 * @file
 * Create table schema for Statistic and populate default types
 */

class StatisticDBA implements DBAInterface{
	/**
	 * Populate database
	 */
	public function script() {
		return array(
			"INSERT INTO statistic_type (name) VALUES 
				('" . StatisticType::BEST_REPLY . "'), 
				('" . StatisticType::COMMENT_COUNT . "'),
				('" . StatisticType::VISIT . "'),
				('" . StatisticType::KARMA . "'),
				('" . StatisticType::LIKE . "')",
		);
	}

	/**
	 * Create table schema
	 *
	 * @return array
	 */
	public function schema() {
		return array(
			'statistic' => array(
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
				'index' => array(
					'type_id' => array('type_id'),
					'type_data_relation' => array('type_id', 'data'),
				),
			),
			'statistic_type' => array(
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
			),
		);
	}
}
