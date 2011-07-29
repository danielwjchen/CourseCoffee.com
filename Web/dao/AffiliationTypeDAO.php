<?php
/**
 * @file
 * Represent affiliation types
 */
class AffiliationTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'affiliation_type';
		parent::__construct($db, $params);
	}

}
