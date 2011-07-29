<?php
/**
 * @file
 * Represent a item_attribute
 */
class ItemAttributeDAO extends AttributeDAO{

	/**
	 * Extend AttributeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->model = 'item';
		parent::__construct($db, $params);

	}

}
