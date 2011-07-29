<?php
/**
 * @file
 * Represent a linkage between a user and a facebook account
 */
class UserFacebookLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'fb_uid', 'id');
		$this->linkage = 'user_facebook_linkage';
		parent::__construct($db, $attr, $params);
	}

}
