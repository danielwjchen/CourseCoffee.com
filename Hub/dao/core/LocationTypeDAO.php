<?php

require_once DAO_PATH . '/abstract/TypeDAO.php';

/**
 * Represent location types
 */
class LocationTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'location_type';
		parent::__construct($db, $params);
	}

}
