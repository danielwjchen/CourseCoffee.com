<?php
/**
 * @file
 * Represent institution-year linkage records
 */
class InstitutionYearLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('institution_id', 'year_id', 'id');
		$this->linkage = 'institution_year_linkage';
		parent::__construct($db, $attr, $params);
	}

}
