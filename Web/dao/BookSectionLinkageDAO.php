<?php
/**
 * @file
 * Represent a linkage between book and section
 */
class BookSectionClassLinkageDAO extends LinkageDAO {

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('book_id', 'section_id', 'id');
		$this->linkage = 'book_section_linkage';
		parent::__construct($db, $attr, $params);
	}

}
