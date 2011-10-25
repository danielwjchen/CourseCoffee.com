<?php
/**
 * @file
 * Represent a linkage between book and section
 */
class BookSectionClassLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'book_id', 
			'child_id'  => 'section_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'book_section_linkage';
	}
}
