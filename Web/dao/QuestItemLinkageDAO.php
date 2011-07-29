<?php
/**
 * @file
 * Represent a linkage between a quest and an item
 */
require_once DAO_PATH . '/abstract/LinkageDAO.php';

class QuestItemLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'item_id', 'id');
		$this->linkage = 'quest_item_linkage';
		parent::__construct($db, $attr, $params);
	}

}
