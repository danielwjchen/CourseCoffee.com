<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class QuestDateLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'quest',
		'child' => 'date',
		'table' => 'quest_date_linkage',
	);
}
