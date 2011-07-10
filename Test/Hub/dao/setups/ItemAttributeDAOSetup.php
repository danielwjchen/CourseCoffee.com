<?php


require_once TEST_CORE_DAO_PATH . '/setups/AttributeDAOSetup.php';

class ItemAttributeDAOSetup extends AttributeDAOSetup implements DAOSetupInterface{

	protected static $dao_class = 'ItemDAOSetup';

}

