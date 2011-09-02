<?php
/**
 * @file
 * Represent quest_attribute types
 */
class QuestAttributeTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct() {
		$this->type = 'quest_attribute_type';
		parent::__construct();
	}

}
