<?php
/**
 * @file
 * Represent institution-location linkage records
 */
class InstitutionLocationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column = array('institution_id', 'location_id', 'id');
		$this->linkage = 'institution_location_linkage';
		$this->setAttribute($this->column);
	}

}
