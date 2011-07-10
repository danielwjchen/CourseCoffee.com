<?php

require_once TEST_CORE_DAO_PATH . '/setups/LinkageDAOSetup.php';

class QuestStatisticLinkageDAOSetup extends LinkageDAOSetup implements DAOSetupInterface{
	protected static $table_attribute = array(
		'parent' => 'quest',
		'child' => 'statistic',
		'table' => 'quest_statistic_linkage',
	);
}
