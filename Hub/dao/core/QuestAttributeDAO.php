<?php

require_once DAO_PATH . '/abstract/AttributeDAO.php';

/**
 * Represent a quest_attribute
 */
class QuestAttributeDAO extends AttributeDAO{

	/**
	 * Extend AttributeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->model = 'quest';
		parent::__construct($db, $params);

	}

}
