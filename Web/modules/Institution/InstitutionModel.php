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
	 */
	public function getInstitutionByDomain($domain) {
		$this->dao['institution']->read(array('domain' => $domain));
		return $this->dao['institution']->attribute;
	}

}
