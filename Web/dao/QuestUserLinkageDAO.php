<?php
/**
 * @file
 * Represent a linkage among quest and user
 */
class QuestUserLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('quest_id', 'user_id', 'id');
		$this->linkage = 'quest_user_linkage';
		$this->setAttribute($this->column);
	}

}
