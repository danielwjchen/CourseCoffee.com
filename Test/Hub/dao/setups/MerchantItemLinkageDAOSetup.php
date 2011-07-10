<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class MerchantItemLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'merchant',
		'child' => 'item',
		'table' => 'merchant_item_linkage',
	);
}
