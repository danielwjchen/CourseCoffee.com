<?php
/**
 * @file
 * Manage institution list related logic
 *
 * @to-do
 * In the future, we might do ip/geo based suggest system
 */
class InstitutionListModel extends Model {

	/**
	 * Access to institution record
	 */
	private $institution_list;

	/**
	 * Implement Model::defineDAO()
	 */
	protected function defineDAO() {
		return array(
			'institution' => array(
				'class' => 'InstitutionListDAO',
				'db'    => self::DEFAULT_DB,
			),
		);
	}

	/**
	 * Get institution by list
	 */
	public function getInstitutionList() {
		$this->dao['institution']->read();

		return $this->dao['institution']->list;
	}
}
