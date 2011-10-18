<?php
/**
 * @file
 * Represent list of related item of a curriculum section
 */
class ItemListDAO extends ListDAO {

	/**
	 * Read records
	 */
	public function read($params) {
		if (isset($params['section_id'])) {
			$sql = "
				SELECT b.isbn FROM item b
				INNER JOIN item_section_linkage bs_linkage
					ON b.id = bs_linkage.item_id
				WHERE bs_linkage.section_id = :section_id
			";
			$sql_param = array('section_id' => $params['section_id']);
			$this->list = $this->db->fetchList($sql, $sql_param);
			return !empty($this->list);
		} 

		Logger::Write('unknow item identifier - ' . print_r($params, true));
		return false;

	}
}
