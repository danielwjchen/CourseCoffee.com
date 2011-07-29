<?php
/**
 * @file
 * Represent a linkage between a merchant and an item
 */
class MerchantItemLinkageDAO extends LinkageDAO{

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('merchant_id', 'item_id', 'id');
		$this->linkage = 'merchant_item_linkage';
		parent::__construct($db, $attr, $params);
	}

}
