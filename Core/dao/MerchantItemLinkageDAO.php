<?php

require_once __DIR__ . '/LinkageDAO.php';

/**
 * Represent a linkage between a merchant and an item
 */
class MerchantItemLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('merchant_id', 'item_id', 'id');
		$this->linkage = 'merchant_item_linkage';
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
