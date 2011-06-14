<?php

require_once __DIR__ . '/LinkageDAO.php';

/**
 * Represent a linkage between two quests
 */
class QuestLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('parent_id', 'child_id', 'id');
		$this->linkage = 'quest_linkage';
		parent::__construct($db, $attr, $params);
	}

}
