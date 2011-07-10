<?php

require_once DAO_PATH . '/abstract/LinkageDAO.php';

/**
 * Represent a linkage between a quest and a affiliation
 */
class QuestAffiliationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'affiliation_id', 'id');
		$this->linkage = 'quest_affiliation_linkage';
		parent::__construct($db, $attr, $params);
	}

}
