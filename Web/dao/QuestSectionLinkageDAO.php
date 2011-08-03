<?php
/**
 * @file
 * Represent a linkage among quest and section
 */
class QuestSectionLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'section_id', 'id');
		$this->linkage = 'quest_section_linkage';
		parent::__construct($db, $attr, $params);
	}

}
