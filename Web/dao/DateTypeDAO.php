<?php
/**
 * @file
 * Represent date types
 */
class DateTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'date_type';
		parent::__construct($db, $params);
	}

}
