<?php

require_once dirname(__FILE__) . '/TypeDAO.php';

/**
 * Represent affiliation types
 */
class AffiliationTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'affiliation_type';
		parent::__construct($db, $params);
	}

	/**
	 * Implement TypeDAO::create()
	 */
	public function create($params) {
		parent::create($params);
	}

	/**
	 * Implement TypeDAO::read()
	 */
	public function read($params) {
		$data = parent::read($params);

	}

	/**
	 * Implement TypeDAO::update()
	 */
	public function update() {
		parent::update();

	}

	/**
	 * Implement TypeDAO::destroy()
	 */
	public function destroy() {
		parent::destroy();

	}

}
