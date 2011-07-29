<?php
/**
 * @file
 * Represent statistic types
 */
class StatisticTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'statistic_type';
		parent::__construct($db, $params);
	}

}
