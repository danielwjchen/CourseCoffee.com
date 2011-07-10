<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';
require_once TEST_CORE_DAO_PATH . '/setups/QuestDAOSetup.php';

class QuestLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'parent',
		'child' => 'quest',
		'table' => 'quest_linkage',
	);


}
