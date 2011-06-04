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
	 * Implement AffiliationDAO::create()
	 */
	public function create($params) {
		$params['type'] = 'college';
		parent::create($params);

	}

	/**
	 * Implement AffiliationDAO::read()
	 */
	public function read($params) {
		$params['type'] = 'college';
		parent::read($params);

	}

	/**
	 * Implement AffiliationDAO::update()
	 */
	public function update() {
		parent::update();

	}

	/**
	 * Implement AffiliationDAO::destroy()
	 */
	public function destroy() {
		parent::destroy();

	}

}
