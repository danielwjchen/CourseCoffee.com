<?php
/**
 * @file
 * Represent list of readings for a class
 */
class ItemListDAO extends ListDAO {

	/**
	 * Read records
	 */
	public function read(array $params = null) {
		if (isset($params['section_id'])) {
			$sql = "
				SELECT 
					b.id,
					b.title,
					b.isbn 
				FROM book b
				INNER JOIN book_section_linkage bs_linkage
					ON b.id = bs_linkage.book_id
				WHERE bs_linkage.section_id = :section_id
			";
			$sql_param = array('section_id' => $params['section_id']);
			$this->list = $this->db->fetchList($sql, $sql_param);
			return !empty($this->list);
		} 

		Logger::Write('unknow book identifier - ' . print_r($params, true));
		return false;

	}
}
