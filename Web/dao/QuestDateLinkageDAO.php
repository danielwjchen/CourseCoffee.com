<?php
/**
 * @file
 * Represent a linkage between a quest and a date
 */
class QuestDateLinkageDAO extends LinkageDAO {

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('quest_id', 'date_id', 'id');
		$this->linkage = 'quest_date_linkage';
		$this->setAttribute($this->column);
	}

}
