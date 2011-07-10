<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class AffiliationTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'college', 
		'study_group', 
		'college_fraternity', 
		'college_sorority'
	);

}

