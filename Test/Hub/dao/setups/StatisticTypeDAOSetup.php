<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class StatisticTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'best_reply',
		'comment_count',
		'traffic',
		'like',
	);

}

