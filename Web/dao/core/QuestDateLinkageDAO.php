<?php
/**
 * @file
 * Represent a linkage between a quest and a date
 */
class QuestDateLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'date_id', 'id');
		$this->linkage = 'quest_date_linkage';
		parent::__construct($db, $attr, $params);
	}

}
