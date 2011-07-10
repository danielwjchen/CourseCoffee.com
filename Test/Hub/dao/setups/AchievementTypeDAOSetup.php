<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class AchievementTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'highest_karma',
		'most_comment',
		'most_best_reply',
	);

}

