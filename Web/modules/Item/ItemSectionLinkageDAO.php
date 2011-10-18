<?php
/**
 * @file
 * Represent a linkage among items and curriculum sections
 */
class ItemSectionClassLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'item_id', 
			'child_id'  => 'section_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'item_section_linkage';
	}
}
