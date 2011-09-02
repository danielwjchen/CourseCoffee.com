<?php
/**
 * @file
 * Represent a linkage between user and institution
 */
class UserInstitutionLinkageDAO extends LinkageDAO {

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('user_id', 'institution_id', 'id');
		$this->linkage = 'user_institution_linkage';
		$this->setAttribute($this->column);
	}

}
