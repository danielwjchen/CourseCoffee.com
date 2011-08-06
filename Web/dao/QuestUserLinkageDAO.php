<?php
/**
 * @file
 * Represent a linkage among quest and user
 */
class QuestUserLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'user_id', 'id');
		$this->linkage = 'quest_user_linkage';
		parent::__construct($db, $attr, $params);
	}

}
