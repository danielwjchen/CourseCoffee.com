<?php

require_once dirname(__FILE__) . '/LinkageDAO.php';

class AffiliationLocationLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('affiliation_id', 'location_id', 'id');
		$this->linkage = 'affiliation_location_linkage';
		parent::__construct($db, $attr, $params);
	}

	/**
	 * Implement LinkageDAO::create()
	 */
	public function create($params) {
		parent::create($params);
	}

	/**
	 * Implement LinkageDAO::read()
	 */
	public function read($params) {
		parent::read($params);
	}

	/**
	 * Implement LinkageDAO::update()
	 */
	public function update() {
		parent::update();
	}

	/**
	 * Implement LinkageDAO::destroy()
	 */
	public function destroy() {
		parent::destroy();
	}

}
