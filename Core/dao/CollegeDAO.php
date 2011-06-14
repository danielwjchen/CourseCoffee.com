<?php

require_once DAO_PATH . 'AffiliationDAO.php';

/**
 * Represent a college object in database
 */
class CollegeDAO extends AffiliationDAO{

	/**
	 * Implement AffiliationDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		if (!empty($params)) {
			$params['type'] = 'college';
		}

		parent::__construct($db, $params);

	}

	/**
	 * Check if the provided parameters are sufficinet to identify a college
	 *
	 * @param array $params
	 *  an associative array of params
	 */
	public function paramsCanIdentifyCollege($params) {
		return (isset($params['college']) || isset($params['college_id']));
	}

	/**
	 * Implement AffiliationDAO::create()
	 */
	public function create($params) {
		$params['type'] = 'college';
		if (isset($params['college_id'])) {
			$params['id'] = $params['college_id'];

		} elseif (isset($params['college'])) {
			$params['name'] = $params['college'];

		}

		parent::create($params);

	}

	/**
	 * Implement AffiliationDAO::read()
	 */
	public function read($params) {
		$params['type'] = 'college';
		parent::read($params);

	}

}
