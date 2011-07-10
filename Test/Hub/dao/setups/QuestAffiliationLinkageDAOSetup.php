<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class QuestAffiliationLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'quest',
		'child' => 'affiliation',
		'table' => 'quest_affiliation_linkage',
	);
}
