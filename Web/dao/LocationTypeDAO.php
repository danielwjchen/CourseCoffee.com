<?php
/**
 * @file
 * Represent location types
 */
class LocationTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->type = 'location_type';
	}

}
