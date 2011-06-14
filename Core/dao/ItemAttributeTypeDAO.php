<?php

require_once __DIR__ . '/TypeDAO.php';

/**
 * Represent item_attribute types
 */
class ItemAttributeTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'item_attribute_type';
		parent::__construct($db, $params);
	}

}
