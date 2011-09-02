<?php
/**
 * @file
 * Represent institution-year linkage records
 */
class InstitutionYearLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('institution_id', 'year_id', 'id');
		$this->linkage = 'institution_year_linkage';
		$this->setAttribute($this->column);
	}

}
