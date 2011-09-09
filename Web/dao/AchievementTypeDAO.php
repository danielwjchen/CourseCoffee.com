<?php
/**
 * @file
 * Represent achievement types
 */
class AchievementTypeDAO extends TypeDAO{

	/**
	 * Extend TypeDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->type = 'achievement_type';
	}

}
