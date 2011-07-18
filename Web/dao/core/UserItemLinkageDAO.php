<?php

require_once DAO_PATH . '/abstract/LinkageDAO.php';

/**
 * Represent a linkage between a user and an item
 */
class UserItemLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'item_id', 'id');
		$this->linkage = 'user_item_linkage';
		parent::__construct($db, $attr, $params);
	}

}
