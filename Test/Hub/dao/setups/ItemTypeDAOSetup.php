<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class ItemTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array('book', 'lab_material', 'lab_equipment');

}

