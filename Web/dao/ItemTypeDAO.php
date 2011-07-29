<?php
/**
 * @file
 * Represent item types
 */
class ItemTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'item_type';
		parent::__construct($db, $params);
	}

}
