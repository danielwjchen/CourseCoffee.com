<?php

require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class MerchantTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array('retail_online', 'retail_local', 'resell');

}

