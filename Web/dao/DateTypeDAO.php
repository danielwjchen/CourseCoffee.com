<?php
/**
 * @file
 * Represent date types
 */
class DateTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->type = 'date_type';
	}

}
