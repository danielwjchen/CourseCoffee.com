<?php
/**
 * @file
 * Represent a linkage between book and section
 */
class BookSectionClassLinkageDAO extends LinkageDAO {

	/**
	 * Extend LinkageDAO::__construct().
	 */
	function __construct() {
		parent::__construct();
		$this->column  = array('book_id', 'section_id', 'id');
		$this->linkage = 'book_section_linkage';
		$this->setAttribute($this->column);
	}

}
