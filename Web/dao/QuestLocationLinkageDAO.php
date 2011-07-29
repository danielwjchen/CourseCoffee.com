<?php
/**
 * @file
 * Represent a linkage between a quest and a affiliation
 */
class QuestLocationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'location_id', 'id');
		$this->linkage = 'quest_location_linkage';
		parent::__construct($db, $attr, $params);
	}

}
