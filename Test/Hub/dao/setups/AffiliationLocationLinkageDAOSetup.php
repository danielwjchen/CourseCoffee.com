<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class AffiliationLocationLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'affiliation',
		'child' => 'location',
		'table' => 'affiliation_location_linkage',
	);
}
