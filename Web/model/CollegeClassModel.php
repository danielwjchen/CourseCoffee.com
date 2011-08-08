<?php
/**
 * @file
 * Over see access to college class records
 */
class CollegeClassModel extends Model {

	/**
	 * Access to college class records
	 */
	private $class_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->class_dao = new CollegeClassDAO($this->db);
	}

	/**
	 * Get college class information by section id
	 *
	 * @param string $section_id
	 */
	public function getClassById($section_id) {
		$this->class_dao->read(array('id' => $section_id));
		return array(
			'content' => $this->class_dao->list,
		);
	}

}
