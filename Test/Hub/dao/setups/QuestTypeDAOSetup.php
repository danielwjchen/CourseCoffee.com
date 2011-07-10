<?php


require_once TEST_CORE_DAO_PATH . '/setups/TypeDAOSetup.php';

class QuestTypeDAOSetup extends TypeDAOSetup implements DAOSetupInterface{

	static $typeArray = array(
		'college_subject', 
		'college_course', 
		'college_section',
		'college_exam',
		'college_reading',
		'college_lab',
		'college_essay',
		'college_quiz',
	);

}

