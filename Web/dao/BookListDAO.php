<?php
/**
 * @file
 * Represent list of readings for a class
 */
class BookListDAO extends DAO {

	/**
	 * Extend DAO::__construct()
	 */
	function __construct($db, $params = null) {
		$attr = array('id', 'isbn');

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Read records
	 */
	public function read($params) {
		if (isset($params['section_id'])) {
			$sql = "
				SELECT b.isbn FROM book b
				INNER JOIN book_section_linkage bs_linkage
					ON b.id = bs_linkage.book_id
				WHERE bs_linkage.section_id = :section_id
			";
			$sql_param = array('section_id' => $params['section_id']);
			$this->list = $this->db->fetch($sql, $sql_param);
			return empty($this->list);
		} 

		throw new Exception('unknow book identifier - ' . print_r($params, true));
		return false;


	}
}
