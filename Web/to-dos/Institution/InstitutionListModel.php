<?php
/**
 * @file
 * Manage institution list_dao records related logics
 */
class InstitutionListModel extends Model {

	/**
	 * Access to college records
	 */
	private $list_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->list_dao = new InstitutionListDAO($this->default_db);
	}

	/**
	 * Get list_dao of colleges available in database
	 *
	 * @to-do:
	 *  maybe an IP address could be passed as param to determine the default 
	 *  option
	 *
	 * @return array
	 *  On success:
	 *   - success:
	 *   - content:
	 *  On failure:
	 *   - error:
	 *   - meessage:
	 */
	public function getInstitutionList() {
		$this->list_dao->read();
		return $this->list_dao->list;
	}
}
