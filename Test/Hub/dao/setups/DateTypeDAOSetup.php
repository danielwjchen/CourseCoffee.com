<?php

require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class DateTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array('begin_date', 'end_date', 'checkpoint');

}
