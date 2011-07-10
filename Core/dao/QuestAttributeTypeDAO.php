<?php

require_once __DIR__ . '/TypeDAO.php';

/**
 * Represent quest_attribute types
 */
class QuestAttributeTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'quest_attribute_type';
		parent::__construct($db, $params);
	}

}
