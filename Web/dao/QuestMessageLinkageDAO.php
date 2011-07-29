<?php
/**
 * @file
 * Represent a linkage between a quest and a message
 */
class QuestMessageLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'message_id', 'id');
		$this->linkage = 'quest_message_linkage';
		parent::__construct($db, $attr, $params);
	}

}
