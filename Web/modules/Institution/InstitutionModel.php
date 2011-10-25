<?php
/**
 * @file
 * Manage Institution related application logic
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class InstitutionModel extends Model {

	/**
	 * Define list of DAOs to access database records
	 */
	protected function defineDAO() {
		return array(
			'institution' => array(
				'dao' => 'InstitutionDAO',
				'db' => self::DEFAULT_DB,
			),
		);
	}

	/**
	 * Get institution by id
	 */
	public function getInstitutionById($institution_id) {
		$this->dao['institution']->read(array('id' => $institution_id));

		return $this->dao['institution']->attribute;
	}

	/**
	 * Get institution by sub domain
	 *
	 * @param string $domain
	 *
	 * @return mixed result
	 *  institution attribute in an associative array or false when research 
	 *  return none
	 */
	public function getInstitutionByDomain($domain) {
		if (!$this->dao['institution']->read(array('domain' => $domain))) {
			return false;
		}
		return $this->dao['institution']->attribute;
	}

}
