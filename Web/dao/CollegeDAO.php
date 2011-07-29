<?php
/**
 * @file
 * Represent a college object in database
 */
class CollegeDAO extends AffiliationDAO{

	/**
	 * Extend AffiliationDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		if (!empty($params)) {
			$params['type'] = AffiliationType::COLLEGE;

		}

		parent::__construct($db, $params);

	}

	/**
	 * Extend AffiliationDAO::create()
	 */
	public function create($params) {
		$params['type'] = 'college';
		return parent::create($params);

	}

	/**
	 * Extend AffiliationDAO::read()
	 */
	public function read($params) {
		$params['type'] = 'college';
		return parent::read($params);

	}

}
