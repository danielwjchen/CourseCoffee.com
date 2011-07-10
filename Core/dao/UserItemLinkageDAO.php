<?php

require_once __DIR__ . '/LinkageDAO.php';

/**
 * Represent a linkage between a user and an item
 */
class UserItemLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('user_id', 'item_id', 'id');
		$this->linkage = 'user_item_linkage';
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
