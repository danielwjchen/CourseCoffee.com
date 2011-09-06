<?php
/**
 * @file
 * Represent a linkage between user and class section
 */
class UserSectionLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'user_id', 
			'child_id'  => 'section_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'user_section_linkage';
	}
}
