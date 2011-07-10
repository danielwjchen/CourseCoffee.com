<?php

require_once DAO_PATH . '/abstract/TypeDAO.php';

/**
 * Represent item_attribute types
 */
class ItemAttributeTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'item_attribute_type';
		parent::__construct($db, $params);
	}

}
