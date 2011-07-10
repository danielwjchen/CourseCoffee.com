<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class LocationTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array('campus', 'building', 'city');

}

