<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class QuestAttributeTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'college_session_num',
		'college_course_num',
		'college_subject_abbr',
	);

}

