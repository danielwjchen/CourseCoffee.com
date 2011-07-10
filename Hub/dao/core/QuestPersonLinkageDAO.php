<?php

require_once DAO_PATH . '/abstract/LinkageDAO.php';

/**
 * Represent a linkage between a quest and a person
 */
class QuestPersonLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'person_id', 'id');
		$this->linkage = 'quest_person_linkage';
		parent::__construct($db, $attr, $params);
	}

}
