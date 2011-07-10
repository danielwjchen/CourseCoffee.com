<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class ItemAttributeTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'isbn',
		'weight',
		'dimension',
	);

}

