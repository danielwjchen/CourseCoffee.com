<?php
/**
 * @file
 * Represent a linkage between user and institution
 */
class UserInstitutionLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'user_id', 
			'child_id'  => 'institution_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'user_institution_linkage';
	}
}
