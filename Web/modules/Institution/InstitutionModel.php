<?php
/**
 * @file
 * Handle Institution related model logic
 */
class InstitutionModel extends Model {

	/**
	 * Implement Model::defineDAO()
	 */
	protected function defineDAO() {
		return array(
			'institution' => array(
				'class' => 'InstitutionDAO',
				'db'    => self::DEFAULT_DB,
			),
		);
	}

	/**
	 * Get college by id
	 */
	public function getInstitutionById($institution_id) {
		$this->dao['institution']->read(array('id' => $institution_id));
		return $this->dao['institution']->attribute;
	}

	/**
	 * Get college by sub domain
	 *
	 * @param string $domain
	 */
	public function getInstitutionByDomain($domain) {
		$this->dao['institution']->read(array('domain' => $domain));
		return $this->dao['institution']->attribute;
	}

}
