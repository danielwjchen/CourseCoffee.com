<?php

require_once DAO_PATH . '/abstract/TypeDAO.php';

/**
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
