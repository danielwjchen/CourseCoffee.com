<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class QuestLocationLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'quest',
		'child' => 'location',
		'table' => 'quest_location_linkage',
	);
}
