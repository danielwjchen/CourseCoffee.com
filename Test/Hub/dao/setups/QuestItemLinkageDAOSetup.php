<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class QuestItemLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'quest',
		'child' => 'item',
		'table' => 'quest_item_linkage',
	);
}
