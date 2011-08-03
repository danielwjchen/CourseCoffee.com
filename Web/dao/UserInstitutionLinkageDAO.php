<?php
/**
 * @file
 * Represent a linkage between user and institution
 */
class UserInstitutionLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'institution_id', 'id');
		$this->linkage = 'user_institution_linkage';
		parent::__construct($db, $attr, $params);
	}

}
