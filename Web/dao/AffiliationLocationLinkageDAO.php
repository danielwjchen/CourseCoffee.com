<?php
/**
 * @file
 * Represent affiliation-location linkage records
 */
class AffiliationLocationLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('affiliation_id', 'location_id', 'id');
		$this->linkage = 'affiliation_location_linkage';
		parent::__construct($db, $attr, $params);
	}

}
