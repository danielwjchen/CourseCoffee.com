<?php
/**
 * @file
 * Manage institution list_dao records related logics
 */
class InstitutionListModel extends Model {
	
	/**
	 * Define list of DAOs to access database records
	 */
	protected function defineDAO() {
		return array(
			'institution_list' => array(
				'dao' => 'InstitutionListDAO',
				'db' => self::DEFAULT_DB,
			),
		);
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
		$this->dao['institution_list']->read();
		return $this->dao['institution_list']->list;
	}

	/**
	 * Get supported institution domain list
	 */
	public function getInstittuionSubDomain() {
		$this->dao['institution_list']->read();
		$result = array();
		foreach ($this->dao['institution_list']->list as $id => $institution) {
			$result[] = $institution['domain'];
		}
		return $result;

	}
}
