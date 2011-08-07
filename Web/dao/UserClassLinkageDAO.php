<?php
/**
 * @file
 * Represent a linkage between user and class section
 */
class UserClassLinkageDAO extends LinkageDAO {

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'section_id', 'id');
		$this->linkage = 'user_section_linkage';
		parent::__construct($db, $attr, $params);
	}

}
