<?php
/**
 * @file
 * Manage Institution related application logic
 */
class InstitutionModel extends Model {

	/**
	 * Access to institution records
	 */
	private $institution_dao;

	/**
	 * Extend Model::__construct()
	 */
	function __construct($sub_domain) {
		parent::__construct($sub_domain);
		$this->institution_dao = new InstitutionDAO($this->default_db);
	}

	/**
	 * Get institution by id
	 */
	public function getInstitutionById($institution_id) {
		$this->institution_dao->read(array('id' => $institution_id));

		return $this->institution_dao->attribute;
	}

	/**
	 * Get institution by sub domain
	 *
	 * @param string $domain
	 */
	public function getInstitutionByDomain($domain) {
		$this->institution_dao->read(array('domain' => $domain));
		return $this->institution_dao->attribute;
	}

}
