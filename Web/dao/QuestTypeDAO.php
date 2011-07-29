<?php
/**
 * @file
 * Represent quest types
 */
class QuestTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'quest_type';
		parent::__construct($db, $params);
	}

}
