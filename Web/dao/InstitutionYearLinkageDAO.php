<?php
/**
 * @file
 * Represent institution-year linkage records
 */
class InstitutionYearLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'institution_id', 
			'child_id'  => 'year_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'institution_year_linkage';
	}
}
