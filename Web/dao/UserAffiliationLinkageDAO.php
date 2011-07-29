<?php
/**
 * @file
 * Represent a linkage between a user and an item
 */
class UserAffiliationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'affiliation_id', 'id');
		$this->linkage = 'user_affiliation_linkage';
		parent::__construct($db, $attr, $params);
	}

}
