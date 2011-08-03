<?php
/**
 * @file
 * Represent institution-location linkage records
 */
class InstitutionLocationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('institution_id', 'location_id', 'id');
		$this->linkage = 'institution_location_linkage';
		parent::__construct($db, $attr, $params);
	}

}
