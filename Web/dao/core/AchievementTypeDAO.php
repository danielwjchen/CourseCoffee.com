<?php

require_once DAO_PATH . '/abstract/TypeDAO.php';

/**
 * Represent achievement types
 */
class AchievementTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->type = 'achievement_type';
		parent::__construct($db, $params);
	}

}
