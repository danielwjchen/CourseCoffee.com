<?php

require_once dirname(__FILE__) . '/LinkageDAO.php';

/**
 * Represent a linkage between a quest and a statistic
 */
class QuestStatisticLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'statistic_id', 'id');
		$this->linkage = 'quest_statistic_linkage';
		parent::__construct($db, $attr, $params);
	}

	/**
	 * Implement LinkageDAO::create()
	 */
	public function create($params) {
		parent::create($params);

	}

	/**
	 * Implement LinkageDAO::read()
	 */
	public function read($params) {
		parent::read($params);

	}

	/**
	 * Implement LinkageDAO::update()
	 */
	public function update() {
		parent::update();

	}

	/**
	 * Implement LinkageDAO::destroy()
	 */
	public function destroy() {
		parent::destroy();

	}

}
