<?php
/**
 * @file
 * Represent a linkage among quest and section
 */
class QuestSectionLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('quest_id', 'section_id', 'id');
		$this->linkage = 'quest_section_linkage';
		$this->setAttribute($this->column);
	}

}
